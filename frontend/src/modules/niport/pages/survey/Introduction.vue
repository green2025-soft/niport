<script setup>
import { ref,reactive, computed, onMounted, inject, watch  } from 'vue'
import { useRoute } from "vue-router";
import { useResourceApiClient } from '@/composables/resourceApiClient'
import { useForm, useImageUrl, runStategy, mediaOfCompanyService,typesOfOwnership  } from '@/utilities/methods'


const {
  customGet
} = useResourceApiClient('', '')

const { faculty_id, category_id }  = defineProps({
  faculty_id: [String, Number],
  category_id: [String, Number],
});

const indoorDepartments = ref([]);
const outdoorDepartments = ref([]);
const consultationDepartments = ref([]);
const diagnosticDepartments = ref([]);
const designations = ref([]);
const diseases = ref([]);





const { form, isSubmitting, formSubmit, createDependencies, editData } = inject('formContext')

const childDefaults = {
  

  // 🔹 Service Medium (checkbox array)
  media_of_company_service: reactive({}),
  media_of_company_service_others: "",

  // 🔹 Patient Data
  total_patient_preivious_month_outdoor_service: "",
  total_patient_preivious_month_outdoor_service_hospital_adm: "",

  // 🔹 Outdoor Departments (checkbox arrays)
  outdoor_departments:reactive({}),

  // 🔹Outdoor Staff  (array based)
  outdoor_staffs:{},


  // 🔹 Indoor Patient
  total_patient_preivious_month_indoor_service: "",
  total_patient_addmitted_indoor_service: "",


    // 🔹 Indoor Departments (checkbox arrays)
  indoor_departments:reactive({}),

   // 🔹Indoor Staff  (array based)
  indoor_staffs:{},

  // 🔹 Diagnostic Department
  department_tests: {},


  // 🔹 Consultation
  department_consultations: reactive({}),
  

  // 🔹 Diseases
  diseases: reactive({}),
  diseases_others: ''

}

onMounted(async () => {
  const cependencies = createDependencies.value
  outdoorDepartments.value      =  cependencies.outdoorDepartments
  indoorDepartments.value       =  cependencies.indoorDepartments
  consultationDepartments.value =  cependencies.consultationDepartments
  diagnosticDepartments.value   =  cependencies.diagnosticDepartments
  
  designations.value            =  cependencies.designations
  diseases.value                =  cependencies.diseases

  if(editData && form.value.id){
    form.value.total_patient_preivious_month_outdoor_service = editData.value.total_patient_preivious_month_outdoor_service
    form.value.total_patient_preivious_month_outdoor_service_hospital_adm = editData.value.total_patient_preivious_month_outdoor_service_hospital_adm
    form.value.media_of_company_service_others = editData.value.media_of_company_service_others
    form.value.media_of_company_service = mapMedia(editData.value.media_of_company_service)
    form.value.total_patient_preivious_month_indoor_service = editData.value.total_patient_preivious_month_indoor_service
    form.value.total_patient_addmitted_indoor_service = editData.value.total_patient_addmitted_indoor_service

    const { outdoor, indoor } = mapDepartments(editData.value.departments)

    form.value.outdoor_departments = outdoor
    form.value.indoor_departments = indoor

    const outdoorStaffs = mapDepartmentStaffs(editData.value.department_staffs, 1)
    const indoorStaffs = mapDepartmentStaffs(editData.value.department_staffs, 2)
    const departmentTests = mapDepartmentTests(editData.value.department_tests)

    form.value.outdoor_staffs = outdoorStaffs
    form.value.indoor_staffs = indoorStaffs
    form.value.department_tests = departmentTests
    form.value.department_consultations = mapConsultations(editData.value.department_consultations)

     const { diseases, other } = mapDiseases(editData.value.diseases)

       form.value.diseases = diseases
       form.value.diseases_others = other
  }
  
})

const mapMedia = (arr) => {
  const result = {}

  arr.forEach(id => {
    result[id] = true
  })

  return result
}

const mapDepartments = (departments = []) => {
  const outdoor = {}
  const indoor = {}

  departments.forEach(item => {
    if (item.type === 1 && item.is_active) {
      outdoor[item.department_id] = true
    }

    if (item.type === 2 && item.is_active) {
      indoor[item.department_id] = true
    }
  })

  return { outdoor, indoor }
}


const mapDepartmentStaffs = (data = [], type='') => {
  const result = {}

  data.forEach(item => {
    if (type && (item.type != type)) return

    const deptId = item.department_id
    const desigId = item.designation_id

    if (!result[deptId]) {
      result[deptId] = {}
    }

    result[deptId][desigId] = {
      no_of_self_doctor: item.self_doctor ?? null,
      no_of_guest_doctor: item.guest_doctor ?? null,
      no_of_consultant: item.consultant ?? null
    }
  })

  return result
}

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

const mapConsultations = (data) => {
  const result = {}

  data.forEach(item => {
    result[item.department_id] = item.total_consultation
  })

  return result
}


const mapDiseases = (data = []) => {
  const diseases = {}
  let other = ''  

  data.forEach(item => {
    diseases[item.disease_id] = true

    if (item.other) {
      other = item.other
    }
  })

  return { diseases, other }
}

Object.keys(childDefaults).forEach((key) => {
  if (!(key in form.value)) {
    form.value[key] = childDefaults[key]
  }
})

function getOutdoorStaff(departmentId, designationId) {
  if (!form.value.outdoor_staffs[departmentId]) {
    form.value.outdoor_staffs[departmentId] = {};
  }

  if (!form.value.outdoor_staffs[departmentId][designationId]) {
    form.value.outdoor_staffs[departmentId][designationId] = {};
  }

  return form.value.outdoor_staffs[departmentId][designationId];
}

function getIndoorStaff(departmentId, designationId) {
  if (!form.value.indoor_staffs[departmentId]) {
    form.value.indoor_staffs[departmentId] = {};
  }

  if (!form.value.indoor_staffs[departmentId][designationId]) {
    form.value.indoor_staffs[departmentId][designationId] = {};
  }

  return form.value.indoor_staffs[departmentId][designationId];
}


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
        <td class="text-center">২০</td>
        <td class="pl-1">প্রতিষ্ঠানের সেবা প্রদানের মাধ্যম </td>
        <td colspan="4" class="p-0">
          <table class="internal-table w-100 table table-bordered mb-0">
          
              <tr  v-for="(value, index) in mediaOfCompanyService()" :key="index">
                <td style="width: 50%; border-right: 1px solid #dee2e6;">{{ value }} </td>
                <td>
                  <div class="form-check-inline">
                    <input class="form-check-input second_block"  type="checkbox" v-model="form.media_of_company_service[index]" name="media_of_company_service" @change="!form.media_of_company_service?.[5] && (form.media_of_company_service_others = '')" value="">
                  </div>
              
              
                    <input type="text" v-if="index==5"   class="form-control from_broder"  v-model="form.media_of_company_service_others" :readonly="!form.media_of_company_service?.[5] === true" >
              
                </td>
              </tr>
            
          </table>
        </td>
        <td></td>
      </tr>
       <tr>
        <td class="text-center">২১</td>
        <td colspan="3" class="pl-1">আউটডোর হতে গত এক মাসে কতজন রোগি সেবা নিয়েছেন?</td>
        <td colspan="2"><input type="text" class="form-control second_block"  v-model="form.total_patient_preivious_month_outdoor_service" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td class="text-center">২২</td>
        <td colspan="3" class="pl-1">আউটডোর হতে সেবা নিয়ে গত এক মাসে কতজন রোগি হাসপাতালে ভর্তি হয়েছেন? </td>
        <td colspan="2"><input type="text" class="form-control second_block"  v-model="form.total_patient_preivious_month_outdoor_service_hospital_adm" value="">
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
      <td rowspan="2" class="text-center">২৩</td>
      <td colspan="5" class="pl-1">আউটডোরে কী কী ইউনিট/বিভাগ আছে? </td>
      <td></td>
    </tr>
    <tr>
      <td colspan="5" class="text-center" >ইউনিট/বিভাগসমূহ </td>
      <td class="text-center">কোড</td>
    </tr>
    <template v-if="outdoorDepartments">
    <tr v-for="outdoorDepartmen in outdoorDepartments" :key="outdoorDepartmen.id">
      <td class="text-center">{{ outdoorDepartmen.serial_no }}</td>
      <td colspan="4" class="pl-1"> {{ outdoorDepartmen.department_name }}</td>
      <td class="text-center">
        <input class="form-check-input third_block " type="checkbox" v-model="form.outdoor_departments[outdoorDepartmen.id]" value="{{ outdoorDepartmen.id }}" >
      </td>
      <td class="text-center">{{ outdoorDepartmen.code_no }}</td>
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
     <tr>
        <td class="text-center">২৪</td>
        <td colspan="6" class="pl-1">আউটডোরে কোন্ ইউনিট/বিভাগে কতজন ষ্টাফ কর্মরত আছেন? </td>
      </tr>
      <tr>
        <td></td>
        <td class="text-center">ইউনিট/বিভাগসমূহ </td>
        <td style="width: 18%;height:25px;" class="text-center">কর্মরত ষ্টাফের পদবী</td>
        <td colspan="3" class="text-center">কর্মরত ষ্টাফের সংখ্যা</td>
        <td></td>
      </tr>
      <template v-for="outdoorDepartmen in outdoorDepartments" :key="outdoorDepartmen.id">
        
      <tr >
        <td class="text-center">{{ outdoorDepartmen.serial_no }}</td>
        <td class="pl-1"> {{ outdoorDepartmen.department_name }}</td>
        <td class="p-0" colspan="4">
          <table class="table table-bordered mb-0">
              <tr>
                 <td style="border-right: 1px solid #dee2e6;" width="35%">&nbsp;</td>
                  <td class="text-center" style="border-right: 1px solid #dee2e6;">নিজস্ব</td>
                  <td class="text-center" style="border-right: 1px solid #dee2e6;">গেষ্ট ডক্টর</td>
                  <td class="text-center">গেষ্ট কনসালটেন্ট</td>
              </tr>
              <tr v-for="designation in designations" :key="designation.id">
                <td style="border-right: 1px solid #dee2e6;">
                  {{ designation.designation_name }}
                  <input type="hidden" v-model="getOutdoorStaff(outdoorDepartmen.id, designation.id).designation_id" value="{{ designation.id }}" class="fourth_block">
                </td>
                <td style="border-right: 1px solid #dee2e6;">
                  <input type="number" v-model="getOutdoorStaff(outdoorDepartmen.id, designation.id).no_of_self_doctor"  class="form-control text-right from_broder" value="">
                </td>
                 <td style="border-right: 1px solid #dee2e6;">
                  <input type="number" v-model="getOutdoorStaff(outdoorDepartmen.id, designation.id).no_of_guest_doctor"  class="form-control text-right from_broder" value="">
                </td>
                <td >
                  <input type="number" v-model="getOutdoorStaff(outdoorDepartmen.id, designation.id).no_of_consultant"  class="form-control text-right from_broder" value="">
                </td>
              </tr>
          </table>
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
      </template>

      <tr>
        <td class="text-center">২৫</td>
        <td colspan="3" class="pl-1">ইনডোর হতে গত এক মাসে কতজন রোগি সেবা নিয়েছেন?</td>
        <td colspan="2">
          <input type="number" class="form-control text-right fifth_block" v-model="form.total_patient_preivious_month_indoor_service" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td  class="text-center">২৬</td>
        <td colspan="3" class="pl-1">ইনডোরে বর্তমানে কতজন রোগি ভর্তি আছেন?</td>
        <td colspan="2">
          <input type="number" class="form-control text-right fifth_block" v-model="form.total_patient_addmitted_indoor_service" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td rowspan="2"  class="text-center">২৭</td>
        <td colspan="5" class="pl-1">ইনডোরে কী কী ইউনিট/বিভাগ আছে?</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="5" class="text-center">ইউনিট/বিভাগসমূহ</td>
        <td class="text-center">কোড</td>
      </tr>
      <template v-if="indoorDepartments">
      <tr v-for="indoorDepartmen in indoorDepartments" :key="indoorDepartmen.id">
        <td class="text-center">{{ indoorDepartmen.serial_no }}</td>
        <td colspan="4" class="pl-1"> {{ indoorDepartmen.department_name }}</td>
        <td class="text-center">
            <input class="form-check-input third_block" type="checkbox" v-model="form.indoor_departments[indoorDepartmen.id]" value="{{ indoorDepartmen.id }}" >
        </td>
        <td class="text-center">{{ indoorDepartmen.code_no }}</td>
      </tr>
    </template>
    <tr>
      <td  class="text-center">২৮</td>
      <td colspan="6" class="pl-1">ইনডোরে কোন্ ইউনিট/বিভাগে কতজন ষ্টাফ কর্মরত আছেন এবং কোন্ ইউনিট/বিভাগে
        বেডের সংখ্যা কত? </td>
    </tr>
    <tr>
      <td></td>
      <td class="pl-1">ইউনিট/বিভাগসমূহ </td>
      <td style="width: 18%;height:25px;" class="pl-1">কর্মরত ষ্টাফের পদবী</td>
      <td colspan="3" class="text-center ">কর্মরত ষ্টাফের সংখ্যা</td>
      <td class="text-center">বেডের সংখ্যা</td>
    </tr>

    <template v-for="indoorDepartmen in indoorDepartments" :key="indoorDepartmen.id">
        
      <tr >
        <td class="text-center">{{ indoorDepartmen.serial_no }}</td>
        <td class="pl-1"> {{ indoorDepartmen.department_name }}</td>
        <td class="p-0"  colspan="4">
          <table class="table table-bordered mb-0">
              <tr>
                 <td style="border-right: 1px solid #dee2e6;" width="35%">&nbsp;</td>
                  <td class="text-center" style="border-right: 1px solid #dee2e6;">নিজস্ব</td>
                  <td class="text-center" style="border-right: 1px solid #dee2e6;">গেষ্ট ডক্টর</td>
                  <td class="text-center">গেষ্ট কনসালটেন্ট</td>
              </tr>
              <tr v-for="designation in designations" :key="designation.id">
                <td style="border-right: 1px solid #dee2e6;">
                  {{ designation.designation_name }}
                  <input type="hidden" v-model="getIndoorStaff(indoorDepartmen.id, designation.id).designation_id" value="{{ designation.id }}" class="fourth_block">
                </td>
                <td style="border-right: 1px solid #dee2e6;">
                    <input type="number" v-model="getIndoorStaff(indoorDepartmen.id, designation.id).no_of_self_doctor"  class="form-control text-right from_broder" value="">
                </td>
                <td style="border-right: 1px solid #dee2e6;">
                  <input type="number" v-model="getIndoorStaff(indoorDepartmen.id, designation.id).no_of_guest_doctor"  class="form-control text-right from_broder" value="">
                </td>
                <td >
                  <input type="number" v-model="getIndoorStaff(indoorDepartmen.id, designation.id).no_of_consultant" class="form-control text-right from_broder" value="">
                </td>
              </tr>
          </table>

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
      </template>
       <tr>
        <td rowspan="2" class="text-center">২৯</td>
        <td colspan="6" class="pl-1">এই হাসপাতালে ডায়াগনষ্টিক কী কী ইউনিট/বিভাগ আছে এবং কোন্ ইউনিট/বিভাগ হতে
          কী কী পরীক্ষা/টেষ্ট করা হয়??</td>
      </tr>
      <tr>
        <td colspan="2" class="text-center">ইউনিট/বিভাগসমূহ</td>
        <td colspan="3" class="text-center">পরীক্ষা/টেষ্টসমূহ</td>
        
        <td class="text-center">কোড</td>
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
                <td class="text-center" >
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
     <tr>
        <td rowspan="2" class="text-center">৩০</td>
        <td colspan="6" class="pl-1">এই হাসপাতালে কনসালটেশনের জন্য কোন্ কোন্ ইউনিট/বিভাগে কতজন কনসালটেন্ট
          বসেন?</td>
      </tr>

      <tr>
        <td colspan="4" class="text-center">ইউনিট/বিভাগসমূহ</td>
        <td class="text-center">কনসালটেন্টের সংখ্যা</td>
        <td class="text-center">কোড</td>
      </tr>

       <tr v-for="consultationDepartment in consultationDepartments" :key="consultationDepartment.id">
          <td class="text-center">{{ consultationDepartment.serial_no}}</td>
          <td colspan="4" class="pl-1">{{ consultationDepartment.department_name }}</td>
          <td class="text-center">
            
            <input type="number" class="from-control text-end from_broder" v-model="form.department_consultations[consultationDepartment.id]"  value="">
          </td>
          <td class="text-center">{{ consultationDepartment.code_no }}</td>
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
          <td :rowspan="(diseases?.length || 0) + 1" class="text-center align-top">৩১</td>
          <td colspan="5" class="pl-1">এই হাসপাতালে নিম্নের কোন্ কোন্ রোগের চিকিৎসা হয়? </td>
          <td></td>
        </tr>

        <tr v-for="disease in diseases" :key="disease.id">
          <td colspan="4" class="pl-1">{{ disease.diseases_name }}
              <br>
              <textarea v-if="disease.diseases_name=='অন্যান্য'"  class="from-control from_broder  w-100" v-model="form.diseases_others"></textarea>
          </td>
          <td class="text-center">
            <input type="checkbox"  class="from-control ninth_block" v-model="form.diseases[disease.id]" :value="disease.id">
          </td>
          <td class="text-center">{{ disease.code_no }}</td>
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