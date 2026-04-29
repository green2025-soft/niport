<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class Upazila extends BaseModel
{
    protected $fillable = [
        'district_id', 'code_no', 'name', 'bn_name', 'url'
    ];

    protected array $searchable = [
        'name', 'bn_name'
    ];

    protected array $searchableRelations = [
        'district' => 'bn_name',
        'division' => 'bn_name'
    ];


    public function district(){
        return $this->belongsTo(District::class, 'district_id');
    }


    public function division(){
        return $this->hasOneThrough(Division::class, District::class, 'id', 'id', 'district_id', 'division_id');
    }


}
