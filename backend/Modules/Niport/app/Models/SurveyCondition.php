<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyCondition extends BaseModel
{


    protected $fillable = [
        'survey_id', 'condition_id', 'sufficient', 'insufficient', 'comments'
    ];




    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }



    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }


}

