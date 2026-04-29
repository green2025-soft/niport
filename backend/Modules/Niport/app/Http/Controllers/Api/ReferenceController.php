<?php

namespace Modules\Niport\Http\Controllers\Api;

use App\Models\User;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\Niport\Models\Category;
use Illuminate\Http\Request;
use Modules\Niport\Models\Condition;
use Modules\Niport\Models\Department;
use Modules\Niport\Models\Designation;
use Modules\Niport\Models\Disease;
use Illuminate\Support\Facades\Cache;
use Modules\Niport\Models\Faculty;

class ReferenceController extends BaseApiController
{
    protected string $title = 'Category';



    public function __construct()
    {
        $this->model = Category::class;
    }

      protected function getLookupData($modelClass, array $filters = [], $withRelations = null)
    {
        $query = $modelClass::where($filters)->orderBy('id');

        if ($withRelations) {
            $query->with($withRelations);
        }

        $data = $query->get();

        return $this->successResponse($data);
    }

    public function faculties($type){
        $category = $this->model::where('type', $type)->with('faculties')->firstOrFail();
        return $this->successResponse($category);
    }


    public function departments(Request $request){
        $categoryId = $request->category_id;
        $unitId     = ($categoryId ==5 || $categoryId==6)?5: $request->unit_id;

        $filters = [
            'category_id'   => $categoryId,
            'unit_id'       =>  $unitId,
        ];
        $this->title    = 'Department';
        return $this->getLookupData(Department::class, $filters, 'tests');
    }


     public function designations(Request $request){
        $this->title    = 'Designation';
        return $this->getLookupData(Designation::class, ['category_id'=> $request->category_id]);
    }


     public function diseases(){
        $this->title    = 'Disease';
        return $this->getLookupData(Disease::class, ['category_id' => 1]);
    }

    public function conditions(){
        $this->title    = 'Condition';
        return $this->getLookupData(Condition::class, ['category_id' => 1]);
    }


    public function listDependencies(Request $request) {
        $type           = $request->type;
        $category       = Category::where('type', $type)->firstOrFail();
        $categoryId     = $category->id;
        // Cache::forget("deps_list_{$categoryId}");
        $cacheData  = Cache::rememberForever("deps_list_{$categoryId}", function () use ($categoryId, $category) {
            $faculties      = $category->faculties;
            $departments    = Department::where('category_id', $category->id)->get();
            return [
                'category'              => $category,
                'faculties'             => $faculties,
                'outdoorDepartments'    => (clone $departments)->where('unit_id',2),
                'indoorDepartments'     => (clone $departments)->where('unit_id',1),
                'users'                 => User::orderByDesc('id')->get()
            ];

         });

        return $this->successResponse($cacheData);

    }

    public function createDependencies(Request $request) {
        $categoryId         = $request->category_id;
        $unitId             = ($categoryId ==5 || $categoryId==6)?5: 4;
        $facultyId          =  $request->faculty_id;
        $faculty            = Faculty::where('category_id', $categoryId)->findOrFail($facultyId);
        // Cache::forget("depsCreate_{$categoryId}");
        $cacheData  = Cache::rememberForever("depsCreate_{$categoryId}", function () use ($categoryId,  $unitId) {
            $category           = Category::findOrFail($categoryId);
            $faculties          = $category->faculties;
            $departments        = Department::where('category_id', $categoryId)->with('tests')->get();
              return [
                'category'                  => $category,
                'faculties'                 => $faculties,
                'outdoorDepartments'        => $departments->where('unit_id', 2)->values(),
                'indoorDepartments'         => $departments->where('unit_id', 1)->values(),
                'consultationDepartments'   => $departments->where('unit_id', 3)->values(),
                'diagnosticDepartments'     => $departments->where('unit_id', $unitId)->values(),
                'designations'              => Designation::where('category_id', $categoryId)->get(),
                'diseases'                  => Disease::where('category_id', 1)->get(),
                'conditions'                => Condition::where('category_id', 1)->get(),
              ];
        });

       $cacheData['faculty'] =  $faculty;
        return $this->successResponse($cacheData);

    }


}
