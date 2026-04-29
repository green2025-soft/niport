<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class Category extends BaseModel
{


    public function faculties()
    {
        return $this->hasMany(Faculty::class, 'category_id');
    }

}
