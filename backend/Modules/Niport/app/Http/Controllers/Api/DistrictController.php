<?php

namespace Modules\Niport\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\Niport\Models\District;
use Modules\Niport\Http\Requests\DistrictRequest;


class DistrictController extends BaseApiController
{
    protected string $title = 'District';

    public function __construct()
    {
        $this->model = District::class;
    }

    public function index(Request $request)
    {
        $query = $this->indexQuery()->with('division:id,name,bn_name');
        if ($request->division_id) {
            $query->where('division_id', $request->division_id);
        }
        return $this->listResponse($query->smartPaginate());
    }

    public function store(DistrictRequest $request)
    {
        $request->validated();
        return $this->saveData($request);
    }

    public function show($id)
    {
        return $this->showData($id, ['division']);
    }

    public function update(DistrictRequest $request, $id)
    {
        $request->validated();
        return $this->updateData($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyData($id);
    }
}
