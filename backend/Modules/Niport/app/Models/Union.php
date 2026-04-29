<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class Union extends BaseModel
{
    protected $fillable = [
        'upazila_id', 'code_no', 'name', 'bn_name', 'url'
    ];

    protected array $searchable = [
        'name', 'bn_name'
    ];

     protected array $searchableRelations = [
        'upazila' => 'bn_name',
    ];


    public function upazila(){
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }



}
