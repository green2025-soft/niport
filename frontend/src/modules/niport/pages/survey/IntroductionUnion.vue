<script setup>
import { ref, computed, onMounted, inject  } from 'vue'
import { useRoute } from "vue-router";
import { useResourceApiClient } from '@/composables/resourceApiClient'
import { typesOfCompany  } from '@/utilities/methods'

const {
customGet
} = useResourceApiClient('', '')

const { faculty_id, category_id }  = defineProps({
  faculty_id: [String, Number],
  category_id: [String, Number],
});


const { form, isSubmitting, formSubmit, createDependencies, editData } = inject('formContext')

const diagnosticDepartments = ref([]);

onMounted(async () => {
  diagnosticDepartments.value   = createDependencies.value.diagnosticDepartments
    if(editData && form.value.id){
       form.value.date_of_interview_receive = editData.value.date_of_interview_receive
       form.value.time_of_interview_receive = editData.value.time_of_interview_receive
       form.value.types_of_company          = editData.value.types_of_company
       form.value.total_patient_preivious_month_outdoor_service = editData.value.total_patient_preivious_month_outdoor_service
       const departmentTests                = mapDepartmentTests(editData.value.department_tests)
       form.value.department_tests          = departmentTests
    }
})

const mapDepartmentTests = (data = []) => {
  const result = {}

  data.forEach(item => {

    const deptId = item.department_id
    const testId = item.test_id

    if (!result[deptId]) {
      result[deptId] = {}
    }

    result[deptId][testId] = {
      test_id: true,
      other_test: item.other_test ?? null,
    }
  })

  return result
}

const childDefaults = {
  date_of_interview_receive:'',
  time_of_interview_receive:'',
  types_of_company:'',
  total_patient_preivious_month_outdoor_service:'',
   department_tests: {},

}

Object.keys(childDefaults).forEach((key) => {
  if (!(key in form.value)) {
    form.value[key] = childDefaults[key]
  }
})

function getDiagnosticTests(departmentId, designationId='') {
  if (!form.value.department_tests[departmentId]) {
    form.value.department_tests[departmentId] = {};
  }

  if (!form.value.department_tests[departmentId][designationId]) {
    form.value.department_tests[departmentId][designationId] = {};
  }

 return form.value.department_tests[departmentId][designationId];

}


</script>

<template>
  
    <tr>
      <td class="text-center">১৯</td>
      <td colspan="3" class="pl-1">সাক্ষাৎকার গ্রহণের তারিখ </td>
      <td colspan="2"><input type="text" class="form-control datepicker second_block" v-model="form.date_of_interview_receive">
      </td>
      <td></td>
    </tr>
    <tr>
      <td class="text-center">২০</td>
      <td colspan="3"  class="pl-1">সাক্ষাৎকার গ্রহণের সময়</td>
      <td colspan="2"><input type="text" class="form-control second_block" v-model="form.time_of_interview_receive">
      </td>
      <td></td>
    </tr>
    <tr v-if="category_id ==6">
      <td></td>
      <td colspan="2" class="pl-1">প্রতিষ্ঠানের ধরন</td>
      <td colspan="3">
        <table class="internal-table">

        
            <tr  v-for="(value, index) in typesOfCompany()" :key="index">
              <td>{{ value }}</td>
              <td>
                <div class="form-check form-check-inline">
                  <input class="form-check-input second_block" type="radio" v-model="form.types_of_company" name="types_of_company" :value="value" >
                </div>
              </td>
            </tr>
          
        </table>
      </td>
      <td></td>
    </tr>

    <tr>
      <td class="text-center">২১</td>
      <td colspan="3"  class="pl-1">গত এক মাসে কতজন রোগি এই প্রতিষ্ঠান হতে সেবা নিয়েছেন? </td>
      <td colspan="2"><input type="text" class="form-control second_block" v-model="form.total_patient_preivious_month_outdoor_service" >
      </td>
      <td></td>
    </tr>
    <tr>
      <td colspan="7" class="text-end">
        <LoadingButton :loading="isSubmitting" class="d-inline-block" size="sm" variant="primary" @click="formSubmit" >
          <span v-if="!form.isSubmit">Update</span>
          <span v-else>Submit</span>
        </LoadingButton>
      </td>
    </tr>
     <tr>
        <td rowspan="2" class="text-center">২২</td>
        <td colspan="6"  class="pl-1">এই প্রতিষ্ঠানে কী কী সেবা প্রদান করা হয়? </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center!important;">ইউনিট/বিভাগসমূহ</td>
        <td colspan="2" style="text-align: center!important;">সেবাসমূহ</td>
        <td style="text-align: center!important;">কোড</td>
        <td></td>
      </tr>
       <template v-for="diagnosticDepartment in diagnosticDepartments" :key="diagnosticDepartment.id">
        
      <tr >
        <td class="text-center">{{ diagnosticDepartment.serial_no }}</td>
        <td colspan="2" class="pl-1"> {{ diagnosticDepartment.department_name }}</td>
        <td class="p-0" colspan="3">
          <table class="table table-bordered mb-0">
              <tr v-for="test in diagnosticDepartment.tests" :key="test.id">
                <td style="border-right: 1px solid #dee2e6;" width="80%">
                  {{  test.test_name }}
                  <textarea   v-show="test.test_name === 'অন্যান্য (Others)'" v-model="getDiagnosticTests(diagnosticDepartment.id, test.id).other_test"  class="form-control seventh_block from_broder" ></textarea>
                </td>
                <td class="text-center" style="vertical-align: top;">
                  <input class="form-control1 seventh_block" type="checkbox" v-model="getDiagnosticTests(diagnosticDepartment.id, test.id).test_id"  :value=" test.test_id"  >
                </td>
              </tr>
          </table>

        </td>
        
        <td class="p-0 align-top">
          <table class="table table-bordered mb-0">  
              <tr v-for="test in diagnosticDepartment.tests" :key="test.id">
                <td class="text-center">
                  {{ test.code_no  }}
                </td>
              </tr>
          </table>

        </td>
         
        
      </tr>
      
      </template>
      <tr>
      <td colspan="7" class="text-end">
        <LoadingButton :loading="isSubmitting" class="d-inline-block" size="sm" variant="primary" @click="formSubmit" >
          <span v-if="!form.isSubmit">Update</span>
          <span v-else>Submit</span>
        </LoadingButton>
      </td>
    </tr>
 
</template>
<style scoped>
td{
  padding: 0px 10px;
  vertical-align: middle;
  font-size: 13px;
  height: auto;
  background: #f9f9f9;

}
.form-control:not(textarea), .custom-file-label, .custom-file-label::after, .custom-file, .custom_text_area, .form-select {
    height: 25px !important;
    padding: 0 12px !important;
}
.form-check-inline{
  padding: 0;
}
.from_broder, input:focus, .from_broder:focus{
  border: 1px solid #dee2e6 !important;
  padding-left: 5px;
  padding-right: 5px;

}
.from_broder:focus{
  box-shadow: none !important;
}
.pl-1{
  padding-left: 5px !important;
}

  .table > :not(caption) > * > *{
    padding: inherit;;
  }
</style>