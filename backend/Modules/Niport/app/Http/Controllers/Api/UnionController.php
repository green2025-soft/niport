<?php

namespace Modules\Niport\Http\Controllers\Api;
use Modules\Core\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Modules\Niport\Models\Union;
use Modules\Niport\Http\Requests\UnionRequest;


class UnionController extends BaseApiController
{
    protected string $title = 'Union';

    public function __construct()
    {
        $this->model = Union::class;
    }

    public function index(Request $request)
    {
        $query = $this->indexQuery()->with(['upazila:id,district_id,name,bn_name','upazila.division','upazila.district:id,name,bn_name']);

        if ($request->upazila_id) {
            $query->where('upazila_id', $request->upazila_id);
        }

        return $this->listResponse($query->smartPaginate());
    }

    public function store(UnionRequest $request)
    {
        $request->validated();
        return $this->saveData($request);
    }

    public function show($id)
    {
        return $this->showData($id, ['upazila','upazila.division', 'upazila.district']);
    }

    public function update(UnionRequest $request, $id)
    {
        $request->validated();
        return $this->updateData($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyData($id);
    }
}
