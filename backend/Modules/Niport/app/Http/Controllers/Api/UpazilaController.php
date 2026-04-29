<?php

namespace Modules\Niport\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\Niport\Models\Upazila;
use Modules\Niport\Http\Requests\UpazilaRequest;


class UpazilaController extends BaseApiController
{
    protected string $title = 'Upazila';

    public function __construct()
    {
        $this->model = Upazila::class;
    }

    public function index(Request $request)
    {
        $query = $this->indexQuery()->with(['division','district:id,name,bn_name']);

         if ($request->district_id) {
            $query->where('district_id', $request->district_id);
        }

        return $this->listResponse($query->smartPaginate());
    }

    public function store(UpazilaRequest $request)
    {
        $request->validated();
        return $this->saveData($request);
    }

    public function show($id)
    {
        return $this->showData($id, ['division', 'district']);
    }

    public function update(UpazilaRequest $request, $id)
    {
        $request->validated();
        return $this->updateData($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyData($id);
    }
}
