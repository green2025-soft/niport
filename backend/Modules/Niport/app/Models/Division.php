<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class Division extends BaseModel
{
    protected $fillable = [
        'name',	'bn_name',	'url'
    ];

    protected array $searchable = ['name',	'bn_name'];
}
