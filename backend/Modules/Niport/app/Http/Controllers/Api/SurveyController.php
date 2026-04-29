<?php

namespace Modules\Niport\Http\Controllers\Api;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\Niport\Models\Division;
use Modules\Niport\Http\Requests\SurveyRequest;
use Modules\Niport\Services\SurveyService;
use Illuminate\Http\Request;
use Modules\Niport\Models\Category;
use Modules\Niport\Models\Survey;

class SurveyController extends BaseApiController
{
    protected string $title = 'Survey';

    public function __construct()
    {
        $this->model = Survey::class;
    }

    public function index(Request $request)
    {
        $categoryId = $request->category_id;
        $type       = $request->type;

        // search
        $search            = $request->search;

        // filter parameters
        $divisionId         = $request->division_id;
        $districtId         = $request->district_id;
        $upazilaId          = $request->upazila_id;
        $unionId            = $request->union_id;
        $facultyId          = $request->faculty_id;
        $outDepartmentId    = $request->out_department_id;
        $inDepartmentId     = $request->in_department_id;
        $createdBy          = $request->created_by;

        $category   = Category::where('type', $type)->firstOrFail();
        $query      = $this->model::orderByDesc('id')->where('category_id', $category->id)
        ->with(
            'faculty:id,faculty_name',
            'division:id,bn_name',
            'district:id,bn_name',
            'upazila:id,bn_name',
            'union:id,bn_name',
            'createdBy:id,name',
            'updateBy:id,name'
        );

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('questionnaire_id', 'like', "%{$search}%")
                ->orWhere('latitude', 'like', "%{$search}%")
                ->orWhere('neighborhood', 'like', "%{$search}%")
                ->orWhere('company_full_name', 'like', "%{$search}%")
                ->orWhere('designation_of_head_of_company', 'like', "%{$search}%")
                ->orWhere('name_of_head_of_company', 'like', "%{$search}%");
            });
        }

        $query->when($divisionId, fn($q) => $q->where('division_id', $divisionId))
      ->when($districtId, fn($q) => $q->where('district_id', $districtId))
      ->when($upazilaId, fn($q) => $q->where('upazila_id', $upazilaId))
      ->when($unionId, fn($q) => $q->where('union_id', $unionId))
      ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
      ->when($outDepartmentId, function ($q) use ($outDepartmentId) {
            $q->whereHas('outDoorDepartments', function ($q2) use ($outDepartmentId) {
                $q2->where('department_id', $outDepartmentId);
            });
        })
      ->when($inDepartmentId, function ($q) use ($inDepartmentId) {
            $q->whereHas('inDoorDepartments', function ($q2) use ($inDepartmentId) {
                $q2->where('department_id', $inDepartmentId);
            });
        })
      ->when($createdBy, fn($q) => $q->where('created_by', $createdBy));


       return $this->listResponse($query->smartPaginate(), ['category'=>$category]);
    }

    public function store(SurveyRequest $request, SurveyService $surveyService)
    {
        $request->validated();
        // return response()->json($surveyService->saveSurvey(), 200, [], JSON_UNESCAPED_UNICODE);
        return $this->createdResponse($surveyService->saveSurvey());
    }

    public function show($id, SurveyService $surveyService)
    {
        $data = $surveyService->getShowData($id);
        return $this->successResponse($data);
    }

    public function update(SurveyRequest $request, SurveyService $surveyService, $id)
    {
        $request->validated();
        return $this->updatedResponse($surveyService->saveSurvey($id));
    }

    public function destroy($id)
    {
        return $this->destroyData($id);
    }
}
