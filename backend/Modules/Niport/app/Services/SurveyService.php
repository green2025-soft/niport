<?php

namespace Modules\Niport\Services;
use Illuminate\Support\Facades\DB;
use Modules\Niport\Models\Disease;
use Modules\Niport\Models\Survey;
use Modules\Niport\Models\SurveyDepartmentStaff;
use Modules\Niport\Models\SurveyDepartmentTest;

class SurveyService {


/**
    Save or Update Survey with related dynamic arrays
 */
 public function saveSurvey($id=''){
    return DB::transaction(function () use ($id) {
        $request    = request();
        // dd($request->all());
        $surveyData = $this->prepareMainData($request);
        // return $surveyData;
         if ($id) {
            $survey = Survey::findOrFail($id);
            $survey->update($surveyData);
         }else{
            $survey = Survey::create($surveyData);
         }
          $this->handleDepartments($survey, $request['outdoor_departments']);
          $this->handleDepartmentStaffs($survey, $request['outdoor_staffs']);

          $this->handleDepartments($survey, $request['indoor_departments'], 2);
          $this->handleDepartmentStaffs($survey, $request['indoor_staffs'], 2);

          $this->handleDepartmentTests($survey, $request['department_tests']);
          $this->handleDepartmentConsultations($survey, $request['department_consultations']);
          $this->handleDiseases($survey, $request['diseases'], $request['diseases_others']);

          $this->handleConditions($survey, $request['conditions'] ?? []);
          return $survey;
     });

 }


 private function prepareMainData($data)
{
    return collect($data)
        ->only((new Survey())->getFillable())
        ->toArray();
}



    private function handleConditions($survey, $conditions)
    {

        $survey->conditions()->delete();
        if (empty($conditions) || !is_array($conditions)) {
            return;
        }

        $insertData = [];

        foreach ($conditions as $conditionId => $condition) {

            // Empty object skip
            if (empty($condition) || !is_array($condition)) {
                continue;
            }

            if (
                empty($condition['sufficient']) &&
                empty($condition['insufficient']) &&
                empty($condition['comments'])
            ) {
                continue;
            }

            $insertData[] = [
                'survey_id'     => $survey->id,
                'condition_id'  => $conditionId,
                'sufficient'    => !empty($condition['sufficient']) ? 1 : null,
                'insufficient'  => !empty($condition['insufficient']) ? 1 : null,
                'comments'      => $condition['comments'] ?? null,
            ];
        }


        if (!empty($insertData)) {
            $survey->conditions()->createMany($insertData);
        }
    }

    private function handleDepartments($survey, $departments, $type=1)
    {
        $survey->departments()->where('type', $type)->delete();
        if (empty($departments)) return;


        $insertData = [];

        foreach ($departments as $deptId => $value) {

            if (!$value) continue;

            $insertData[] = [
                'survey_id'     => $survey->id,
                'department_id' => $deptId,
                'is_active'     => $value ? 1 : null,
                'type'          => $type
            ];
        }

        if (!empty($insertData)) {
            $survey->departments()->createMany($insertData);
        }
    }

    private function handleDepartmentStaffs($survey,  $staffs, $type=1){
        $survey->departmentStaffs()->where('type', $type)->delete();
        if (empty($staffs) || !is_array($staffs)) return;

         $insertData = [];
         foreach ($staffs as $deptId => $types) {
             if (empty($types)) continue;

             foreach ($types as $desigId => $data) {
                 if (empty($data)) continue;

                  if (
                        empty($data['no_of_self_doctor']) &&
                        empty($data['no_of_guest_doctor']) &&
                        empty($data['no_of_consultant'])
                    ) {
                        continue;
                    }

                $insertData[] = [
                    'survey_id'         => $survey->id,
                    'department_id'     => $deptId,
                    'designation_id'    => $desigId,
                    'self_doctor'       => $data['no_of_self_doctor'] ?? null,
                    'guest_doctor'      => $data['no_of_guest_doctor'] ?? null,
                    'consultant'        => $data['no_of_consultant'] ?? null,
                    'type'              => $type
                ];
             }
         }

         if (!empty($insertData)) {
            $survey->departmentStaffs()->createMany($insertData);
        }
    }


       private function handleDiseases($survey, $diseases, $other='')
    {
        $survey->diseases()->delete();
        if (empty($diseases)) return;

        $otherId = '';
        if($other){
            $disease = Disease::where('diseases_name', 'অন্যান্য')->first();
            $otherId = $disease->id??'';
        }


        $insertData = [];

        foreach ($diseases as $deptId => $value) {

            if (!$value) continue;

            $insertData[] = [
                'survey_id'     => $survey->id,
                'disease_id'    => $deptId,
                'other'         => $otherId == $deptId ? $other:''
            ];
        }
        if (!empty($insertData)) {
            $survey->diseases()->createMany($insertData);
        }
    }

    private function handleDepartmentTests($survey, $departments)
    {
        $survey->departmentTests()->delete();
        if (empty($departments) || !is_array($departments)) {
            return;
        }


        $insertData = [];

        foreach ($departments as $departmentId => $tests) {

            if (empty($tests) || !is_array($tests)) continue;

            foreach ($tests as $testId => $data) {

                // empty object skip (e.g. "41": {})
                if (empty($data) || !is_array($data)) continue;

                if (empty($data['test_id'])) continue;

                $insertData[] = [
                    'survey_id'    => $survey->id,
                    'department_id'=> $departmentId,
                    'test_id'      => $testId,
                    'other_test'   => $data['other_test'] ?? null,
                ];
            }
        }

        if (!empty($insertData)) {
            $survey->departmentTests()->createMany($insertData);
        }
    }


    private function handleDepartmentConsultations($survey, $consultations)
    {
        if (empty($consultations) || !is_array($consultations)) {
            return;
        }

        $survey->departmentConsultations()->delete();

        $insertData = [];

        foreach ($consultations as $departmentId => $total) {

            if (empty($total)) continue;

            $insertData[] = [
                'survey_id'             => $survey->id,
                'department_id'         => $departmentId,
                'total_consultation'    => $total,
            ];
        }

        if (!empty($insertData)) {
            $survey->departmentConsultations()->createMany($insertData);
        }
    }


    public function getShowData($id){
        $relation = [
            'faculty:id,faculty_name,code_no',
            'category:id,type,checklist_no,category_name',
            'division:id,bn_name',
            'district:id,division_id,bn_name',
            'upazila:id,district_id,bn_name',
            'union:id,upazila_id,bn_name',
            'conditions:id,survey_id,condition_id,sufficient,insufficient,comments',
            'conditions.condition:id,serial_no,condition_name',
            'departments',
            'outDoorDepartments',
            'outDoorDepartments.department',
            'inDoorDepartments',
            'inDoorDepartments.department',
            'departmentStaffs',
            'diseases:id,survey_id,disease_id,is_active,other',
            'diseases.disease:id,diseases_name,code_no',
            'departmentConsultations:id,survey_id,department_id,total_consultation',
            'departmentConsultations.department:id,serial_no,department_name,code_no'
        ];

        $data = Survey::with($relation)->findOrFail($id);
        $categoryId = $data->category_id;
        $unitId             = ($categoryId ==5 || $categoryId==6)?5: 4;
        $data->outDoorDepartmentStaffs = $this->getDepartmentStaffs($id, 1, 2);
        $data->inDoorDepartmentStaffs = $this->getDepartmentStaffs($id, 2, 1);
        $data->departmentTests = $this->getDepartmentTest($id, $unitId);
        return $data;

    }


    private function getDepartmentStaffs($id, $type, $unitId)
    {
        $data = SurveyDepartmentStaff::with(['department:id,serial_no,department_name,code_no', 'designation'])
            ->where([
                'survey_id' => $id,
                'type' => $type
            ])
            ->whereHas('department', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->get();

        return $data->groupBy('department_id')
            ->map(function ($items) {
                return [
                    'department_id' => $items->first()->department_id,
                    'department' => $items->first()->department,
                    'staffs' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'designation' => $item->designation,
                            'self_doctor' => $item->self_doctor,
                            'guest_doctor' => $item->guest_doctor,
                            'consultant' => $item->consultant,
                        ];
                    })->values()
                ];
            })
            ->values();
    }

    private function getDepartmentTest($id, $unitId)
    {
        $data = SurveyDepartmentTest::with(['department:id,serial_no,department_name,code_no', 'departmentTest'])
            ->where(['survey_id' => $id])
            ->whereHas('department', function ($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            })
            ->get();

        return $data
        ->groupBy('department_id')
        ->map(function ($items) {
            $item = $items->first();
            return [
                'department_id' => $item->department_id,
                'department'    => $item->department,
                'tests' => $items->map(function ($item) {
                    return [
                        'test_name'  => $item->departmentTest->test_name ?? '',
                        'other_test' => $item->other_test,
                        'code_no'    => $item->departmentTest->code_no??'',
                    ];
                })->values()
            ];
        })
        ->values();
    }

}
