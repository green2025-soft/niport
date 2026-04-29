<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyDepartment extends BaseModel
{


    protected $fillable = [
        'survey_id', 'department_id', 'is_active', 'type'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }



    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }


}

