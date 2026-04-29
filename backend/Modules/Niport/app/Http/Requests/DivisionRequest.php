<?php

namespace Modules\Niport\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class DivisionRequest extends BaseRequest
{
    protected array $rules = [
         'name'         => ['required', 'string', 'max:255'],
         'bn_name'      => ['required', 'string', 'max:255'],
         'url'          => ['nullable', 'string', 'max:255'],
    ];

}
