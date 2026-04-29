<?php

namespace Modules\Niport\Models;

use App\Models\User;
use Modules\Core\Models\BaseModel;
class Survey extends BaseModel
{

    protected $table = 'survey_informations';

    protected $fillable = [
        'questionnaire_id',
        'session',
        'category_id',
        'faculty_id',
        'company_full_name',
        'designation_of_head_of_company',
        'name_of_head_of_company',
        'division_id',
        'district_id',
        'upazila_id',
        'union_id',
        'latitude',
        'longitude',
        'moholla_name',
        'word_no',
        'road_no',
        'holding_no',
        'land_phone_no',
        'cell_phone_no',
        'email_no',
        'web_address',
        'contact_no_others',
        'id_no_of_company',
        'gis_no_of_company',
        'founding_period_of_company',
        'previous_name_of_company',
        'registration_no_of_company',
        'license_no_of_company',
        'expiration_date_license_of_company',
        'no_of_bed_ward',
        'no_of_bed_cabin',
        'run_stategy',
        'run_stategy_others',
        'name_of_interview_provider',
        'designation_of_interview_provider',
        'mobile_of_interview_provider',
        'media_of_company_service',
        'media_of_company_service_others',
        'total_patient_preivious_month_outdoor_service',
        'total_patient_preivious_month_outdoor_service_hospital_adm',
        'date_of_interview_receive',
        'time_of_interview_receive',
        'total_patient_preivious_month_indoor_service',
        'total_patient_addmitted_indoor_service',
        'types_of_ownership',

        // Final Inter View
         // interview provider
         'interview_provider_name',
         'interview_provider_id',
         'interview_provider_mobile',
         'interview_provider_date',

         // interview receipient
         'interview_receipient_name',
         'interview_receipient_id',
         'interview_receipient_mobile',
         'interview_receipient_date',

         'created_by', 'updated_by'
    ];



    protected $dateFields = [
        'interview_receipient_date',
        'interview_provider_date',
        'date_of_interview_receive',
    ];

    protected $casts = [
        'media_of_company_service' => 'array',
    ];

    /**
     * Auto format date fields
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->dateFields)) {
            $value = dbDateFormat($value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Auto set created_by & updated_by
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        static::deleting(function ($survey) {
            $survey->conditions()->delete();
            $survey->departments()->delete();
            $survey->departmentStaffs()->delete();
            $survey->diseases()->delete();
            $survey->departmentTests()->delete();
            $survey->departmentConsultations()->delete();
        });
    }


    public function setMediaOfCompanyServiceAttribute($value)
    {
        if (empty($value) || !is_array($value)) {
            $this->attributes['media_of_company_service'] = json_encode([]);
            return;
        }

        $filtered = array_keys(array_filter($value, function ($val) {
            return $val === true || $val === 1 || $val === "true";
        }));

        $this->attributes['media_of_company_service'] = json_encode(
            array_map('intval', $filtered)
        );
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }



    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

     public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

     public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function upazila()
    {
        return $this->belongsTo(Upazila::class, 'upazila_id');
    }

    public function union()
    {
        return $this->belongsTo(Union::class, 'union_id');
    }


    public function conditions()
    {
        return $this->hasMany(SurveyCondition::class, 'survey_id');
    }

    public function departments()
    {
        return $this->hasMany(SurveyDepartment::class, 'survey_id');
    }

     public function outDoorDepartments()
    {
        return $this->hasMany(SurveyDepartment::class, 'survey_id')->where('type',1);
    }

    public function inDoorDepartments()
    {
        return $this->hasMany(SurveyDepartment::class, 'survey_id')->where('type',2);
    }

    public function departmentStaffs()
    {
        return $this->hasMany(SurveyDepartmentStaff::class, 'survey_id');
    }

     public function outDoorDepartmentStaffs()
    {
        return $this->hasMany(SurveyDepartmentStaff::class, 'survey_id')->where('type',1);
    }

    public function inDoorDepartmentStaffs()
    {
        return $this->hasMany(SurveyDepartmentStaff::class, 'survey_id')->where('type',1);
    }

    public function diseases()
    {
        return $this->hasMany(SurveyDisease::class, 'survey_id');
    }

    public function departmentTests()
    {
        return $this->hasMany(SurveyDepartmentTest::class, 'survey_id');
    }

    public function departmentConsultations()
    {
        return $this->hasMany(SurveyDepartmentConsultation::class, 'survey_id');
    }


}



// ACID Atomicity (অ্যাটোমিসিটি), Consistency (কনসিস্টেন্সি), Isolation (আইসোলেশন), Durability (ডিউরেবিলিটি)

// Encapsulation
// Inheritance
// Polymorphism
// Abstraction
