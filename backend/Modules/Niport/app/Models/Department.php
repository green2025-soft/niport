<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class Department extends BaseModel
{


    public function tests(){
        return $this->hasMany(DepartmentTest::class);
    }


}
