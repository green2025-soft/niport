<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyDepartmentStaff extends BaseModel
{

    protected $table = 'survey_department_staffs';


    protected $fillable = [
        'survey_id', 'department_id','designation_id', 'self_doctor', 'guest_doctor', 'consultant', 'type'
    ];



      public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

       public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }



    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }


}

