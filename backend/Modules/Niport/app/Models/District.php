<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class District extends BaseModel
{
    protected $fillable = [
        'division_id', 'code_no', 'name', 'bn_name', 'lat', 'lon', 'url'
    ];

    protected array $searchable = [
        'name', 'bn_name'
    ];

    protected array $searchableRelations = [
        'division' => 'bn_name'
    ];

    public function division(){
        return $this->belongsTo(Division::class, 'division_id');
    }


}
