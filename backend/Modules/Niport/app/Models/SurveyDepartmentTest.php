<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyDepartmentTest extends BaseModel
{


    protected $fillable = [
        'survey_id', 'department_id', 'test_id', 'other_test'
    ];



   public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

       public function departmentTest()
    {
        return $this->belongsTo(DepartmentTest::class, 'test_id');
    }



    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }


}

