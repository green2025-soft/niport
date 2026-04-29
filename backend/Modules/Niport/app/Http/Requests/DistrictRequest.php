<?php

namespace Modules\Niport\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class DistrictRequest extends BaseRequest
{
    protected array $rules = [
        'division_id'   => ['required', 'exists:divisions,id'],
        'name'          => ['required', 'string', 'max:255'],
        'bn_name'       => ['required', 'string', 'max:255'],
        'code_no'       => ['nullable', 'string', 'max:255'],
        'lat'           => ['nullable', 'string', 'max:255'],
        'lon'           => ['nullable', 'string', 'max:255'],
        'url'           => ['nullable', 'string', 'max:255']
    ];
}
