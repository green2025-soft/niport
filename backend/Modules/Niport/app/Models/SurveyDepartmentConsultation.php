<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyDepartmentConsultation extends BaseModel
{


    protected $fillable = [
        'survey_id', 'department_id', 'total_consultation'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }



    public function survey()
    {
        return $this->hasMany(Survey::class, 'survey_id');
    }


}

