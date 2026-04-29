<?php

namespace Modules\Niport\Models;

use Modules\Core\Models\BaseModel;
class SurveyDisease extends BaseModel
{


    protected $fillable = [
        'survey_id', 'disease_id', 'is_active', 'other'
    ];


    public function disease()
    {
        return $this->belongsTo(Disease::class, 'disease_id');
    }



    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

}

