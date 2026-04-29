<?php

namespace Modules\Niport\Http\Requests;

use Modules\Core\Http\Requests\BaseRequest;

class SurveyRequest extends BaseRequest
{
    protected array $rules = [
         'session'         => ['required', 'string', 'max:255']
    ];

}
