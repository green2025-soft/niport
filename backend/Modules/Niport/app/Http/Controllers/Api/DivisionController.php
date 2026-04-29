<?php

namespace Modules\Niport\Http\Controllers\Api;
use Modules\Core\Http\Controllers\Api\BaseApiController;

use Modules\Niport\Models\Division;
use Modules\Niport\Http\Requests\DivisionRequest;


class DivisionController extends BaseApiController
{
    protected string $title = 'Division';

    public function __construct()
    {
        $this->model = Division::class;
    }

    public function index()
    {
       return $this->indexData();
    }

    public function store(DivisionRequest $request)
    {
        $request->validated();
        return $this->saveData($request);
    }

    public function show($id)
    {
        return $this->showData($id);
    }

    public function update(DivisionRequest $request, $id)
    {
        $request->validated();
        return $this->updateData($request, $id);
    }

    public function destroy($id)
    {
        return $this->destroyData($id);
    }
}
