<?php

namespace Modules\Niport\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class UpazilaRequest extends BaseRequest
{
    protected array $rules = [
        'division_id'   => ['required', 'exists:divisions,id'],
        'district_id'   => ['required', 'exists:districts,id'],
        'name'          => ['required', 'string', 'max:255'],
        'bn_name'       => ['required', 'string', 'max:255'],
        'code_no'       => ['nullable', 'string', 'max:255'],
        'url'           => ['nullable', 'string', 'max:255']
    ];
}
