<script setup>
import { ref, computed, onMounted, provide, reactive,watch    } from 'vue'
import { useRoute, useRouter  } from "vue-router";
import { useResourceApiClient } from '@/composables/resourceApiClient'
import {useForm, typesOfOwnership,runStategy  } from '@/utilities/methods'

import Introduction from "@/modules/niport/pages/survey/Introduction.vue";
import IntroductionUnion from "@/modules/niport/pages/survey/IntroductionUnion.vue";
import vSelect from 'vue-select'


const route = useRoute();
const router = useRouter()
const faculty_id = ref(route.params.faculty_id || null)
let category_id = ref(route.params.category_id || null)
//  faculty_id.value = computed(() => route.params.faculty_id);
//  category_id.value = computed(() => route.params.category_id);
const edit_id   = computed(() => route.params.edit_id);

//  Setup
const title = 'Introduction and Consent'
const bUrl =`niport/surveys`;

const {
  update,
  create,
  getOne,
  formErrors,
  isSubmitting,
  customGet,
  isLoading
} = useResourceApiClient(bUrl, title)




const conditions = ref([]);
const createDependencies = ref([]);
let editGetData = ''
let editData = ref('')
const isEditing = ref(false)
const isSpinner = ref(false)
onMounted(async () => {
   try {
    let session = form.value.session
    isSpinner.value= true
    if(edit_id.value){
       editGetData = await getOne(edit_id.value)
       editData.value = editGetData
      category_id.value = editGetData.category_id
      faculty_id.value = editGetData.faculty_id
      form.value.isSubmit = false
      form.value.id =  editGetData.id
      session = editGetData.session
      
      form.value.category_id =  editGetData.category_id
      form.value.faculty_id =  editGetData.faculty_id
      isEditing.value = true
    }
    
    
    const dependencies            = await customGet('/niport/create-dependencies', {'category_id':category_id.value, 'faculty_id':faculty_id.value});

    if (dependencies && edit_id.value){
      form.value.session = await session;
      assignFormData(editGetData);
      setTimeout(() => {
        isEditing.value = false
      }, 0)
    }
    createDependencies.value      =  dependencies
    conditions.value              = dependencies.conditions
   }catch (error) {
      router.replace('/niport/404')
   }
  
  
  await loadDistricts(form.value.division_id);

  isSpinner.value= false
  
})


const assignFormData = (data) => {

  Object.keys(form.value).forEach(key => {
    if(Array.isArray(data[key])){
      if (key=='conditions'){
         Object.entries(data[key]).forEach(([id, item]) => {
          
          // example assign
          form.value.conditions[item.condition_id] = {
            sufficient: item.sufficient?true: false,
            insufficient: item.insufficient?true: false,
            comments: item.comments || ''
          }
        })
      }
      
    }else{
       form.value[key] = data[key]??''
    }
  })
}

const { form, reset } = useForm({
  id:"",
  isSubmit:true,
  session:'',
  category_id:category_id.value,
  faculty_id:faculty_id.value,
  // 🔹 Basic Info
  questionnaire_id: "",
  company_full_name: "",
  designation_of_head_of_company: "",
  name_of_head_of_company: "",

  // 🔹 Location
  division_id: "",
  district_id: "",
  upazila_id: "",
  union_id: "",
  latitude: "",
  longitude: "",

  // 🔹 Address Details
  moholla_name: "",
  word_no: "",
  road_no: "",
  holding_no: "",

  // 🔹 Contact
  land_phone_no: "",
  cell_phone_no: "",
  email_no: "",
  web_address: "",
  contact_no_others: "",

  // 🔹 Company Extra Info
  id_no_of_company: "",
  gis_no_of_company: "",
  founding_period_of_company: "",
  previous_name_of_company: "",

  // 🔹 Registration & License
  registration_no_of_company: "",
  license_no_of_company: "",
  expiration_date_license_of_company: "",

  // 🔹 Bed Info
  no_of_bed_ward: "",
  no_of_bed_cabin: "",

  // 🔹 Strategy (radio)
  run_stategy: "",
  run_stategy_others: '',

  // 🔹 Interview Info
  name_of_interview_provider: "",
  designation_of_interview_provider: "",
  mobile_of_interview_provider: "",


  // 🔹 Ownership
   types_of_ownership: null,

  // 🔹 Conditions

  conditions:{},
    // 🔹 Final Interview section
  name_of_interview_provider_final: "",
  interview_provider_id: "",
  mobile_of_interview_provider_final: "",
  interview_provider_date: "",

  interview_receipient_name: "",
  interview_receipient_id: "",
  interview_receipient_mobile: "",
  interview_receipient_date: "",
})


function getConditions(id) {
  if (typeof form.value.conditions !== 'object' || form.value.conditions === null) {
    form.value.conditions = {}
  }

  if (!form.value.conditions[id]) {
    form.value.conditions[id] = {}
  }

  return form.value.conditions[id]
}


provide('formContext', {
  form,
  isSubmitting,
  formSubmit,
  createDependencies, editData
})


 function isUnion(){
   return (category_id.value ==5 || category_id.value ==6)? true:false
 }




const districts = ref([])
const upazilas = ref([])
const unions = ref([])

const loadDistricts = async (divisionId) => {
  if(!divisionId) return;
   districts.value = await customGet('/niport/district', { division_id: divisionId })
}

const loadUpazilas = async (districtId) => {
  if(!districtId) return;
   upazilas.value = await customGet('/niport/upazila', { district_id: districtId })
}

const loadUnions  = async (upazilaId) => {
  if(!upazilaId) return;
   unions.value = await customGet('/niport/union', { upazila_id: upazilaId })
}


watch(() => form.value.division_id, (val) => {
  loadDistricts(val)

  if (!isEditing.value) {
    form.value.district_id = ''
  }
})

watch(() => form.value.district_id, (val) => {
  loadUpazilas(val)

  if (!isEditing.value) {
    form.value.upazila_id = ''
  }
})

watch(() => form.value.upazila_id, (val) => {
  loadUnions(val)

  if (!isEditing.value) {
    form.value.union_id = ''
  }
})


const errors = ref([])

async function formSubmit() {
  
  if(form.value.session !='হ্যাঁ' && form.value.session !='না') return
   try {

    if (form.value.session === 'না') {
       const keep = {
        id: form.value.id,
        session: form.value.session,
        isSubmit:form.value.isSubmit,
        category_id:form.value.category_id,
        faculty_id:form.value.faculty_id,
      }

      Object.keys(form.value).forEach(key => {
        form.value[key] = ''
      })

      Object.assign(form.value, keep)
    }

    if (form.value.id) {
       await update(form.value.id, form.value)
    }else{
      const crudData = await create(form.value)
      form.value.id = crudData.id
    }
   }catch (error) {
    errors.value = formErrors.value
  }

}


 function handleChange() {
    isSpinner.value = true
    setTimeout(() => {
         isSpinner.value = false
    }, 800) 
    
  }



</script>

<template>
    <!-- Delete Confirmation -->
     
   


  <div class="px-3" >
    <div class="mx-auto w-100 " >
      <BCard 
       header-tag="header"
      header-bg-variant="info"
      header-text-variant="white"
      >
      <template #header>
        <h2 class="card-title fw-bold text-white " >
            <!-- <i class="fas fa-edit"></i> {{ title }} -->
          </h2>
          <div class="card-tools">
              <RouterLink class="btn btn-primary btn-sm" :to="`/niport/survey/${createDependencies?.category?.type}`"><i class="fas fa-list"></i> Survey List</RouterLink>
            </div>
      </template>
       
         <BCardText >
          <h4 class="text-center fw-bold">{{ title }}</h4>
            
            <div class="page " >
              
               <div class="introduction ">
                আস্সালামু আলাইকুম/আদাব<br>
                স্বাস্থ্য ও পরিবার কল্যাণ মন্ত্রণালয়ের স্বাস্থ্য শিক্ষা ও পরিবার কল্যাণ বিভাগের অধীন জাতীয়
                জনসংখ্যা গবেষণা ও প্রশিক্ষণ ইনস্টিটিউট (নিপোর্ট) <span class="fw-bold fs-5">Research Study on Developing Directory
                  and Service Profile of Public, Private and NGO Sector Health Facility</span> শীর্ষক একটি
                গবেষণা পরিচালনা করছে। নিপোর্ট-এর পক্ষে আমরা গণ উন্নয়ন সংস্থা থেকে এসেছি। গণ উন্নয়ন সংস্থা
                গবেষণাটির কার্যক্রম বাস্তবায়ন করছে। এ গবেষণার উদ্দেশ্য হলো বাংলাদেশের হেল্থ
                ফ্যাসিলিটি/স্বাস্থ্যসেবা প্রতিষ্ঠানসমূহের ডাইরেক্টরী এবং স্বাস্থ্য সেবা বিষয়ক প্রোফাইল প্রণয়ণ
                করা। এই লক্ষ্যে দেশের সরকারি, বে-সরকারি ও এনজিও সেক্টরের মাধ্যমে পরিচালিত স্বাস্থ্যসেবা
                প্রতিষ্ঠানসমূহ হতে তথ্য সংগ্রহ করা হবে এবং এ সম্পর্কিত তথ্য পর্যালোচনা করে একটি প্রতিবেদন প্রণয়ণ
                করা হবে । সংগৃহীত তথ্যের ভিত্তিতে একটি ওয়েব বেইজড প্রোফাইল তৈরী করা হবে যার মাধ্যমে দেশের সকল
                অঞ্চলের স্বাস্থ্যসেবা প্রতিষ্ঠান ও সেবা প্রাপ্তির সুযোগ সম্পর্কে জানা যাবে । এই গবেষণায়
                সাক্ষাৎকার গ্রহণে ১৫ মিনিটের মত সময় লাগবে। আপনার দেয়া তথ্য সস্পূর্ণভাবে গোপন রাখা হবে এবং অন্য
                কারো নিকট প্রকাশ করা হবে না। গবেষণার রিপোর্টে কোথাও আপনার নাম লেখা থাকবে না।<br>

                এই গবেষণায় অংশ গ্রহণ সস্পূর্ণভাবে আপনার ইচ্ছার উপর নির্ভর করছে এবং আপনি ইচ্ছা করলে কোন একটি
                প্রশ্নের বা সস্পূর্ণ প্রশ্নমালার উত্তর নাও দিতে পারেন। তারপরও আমি আশা করব আপনি এই গবেষণায় তথ্য
                সংগ্রহে অংশ গ্রহণ করবেন কারণ আপনার মতামত এই গবেষণার জন্য অত্যন্ত গুরুত্বপূর্ণ।<br>

                এখন আপনি গবেষণা সম্পর্কে আরও কিছু জানতে চাইলে আমাকে জিজ্ঞাসা করতে পারেন।<br>

                আমি কি এখন আপনার সাক্ষাৎকার নেয়া শুরু করতে পারি ?
                 

                   <div class="form-check-inline" style="margin-left: 40px;">
                    <label  for="session_yes"><strong style="font-size: 22px;">হ্যাঁ &nbsp;</strong>
                      <input  type="radio" id="session_yes" @change="handleChange" v-model="form.session" class="option-input" name="session" value="হ্যাঁ" >
                      <span class="checkbox"></span>
                      </label>
                    
                  </div>
                  <div class=" form-check-inline">
                    <label  for="session_no"><strong style="font-size: 22px;">না  &nbsp;</strong>
                      <input  type="radio" id="session_no" @change="handleChange"  v-model="form.session" class="option-input" name="session" value="না">
                      <span class="checkbox"></span>
                     </label>
                    
                  </div>

              </div>

            </div>
       
            <CenteredSpinner v-if="isSpinner"  />
            <!-- <template  v-show="!isSpinner"> -->
              <template v-if="form.session =='হ্যাঁ' && !isSpinner">
                <!-- <br> -->
                <!-- <input type="text" class="form-control keyword" placeholder="Search Keyword"> -->
              <br>
              <div class="table-responsive">
                <table class="table table-bordered parent-table" id="parent_table">
                  <tbody>
                    <tr>
                      <td colspan="7" class="text-end" style="padding-right: 10px;">কোড নং</td>
                    </tr>
                    <tr>
                      <td></td>
                      <td>
                        <label class="fw-bold text-primary pl-1" >Questionnaire ID </label>
                      </td>
                      <td colspan="4">
                        <input type="text"  v-model="form.questionnaire_id" class="form-control first_block" >
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="parent_table_serial text-center" style="width: 2%;">০১ </td>
                      <td  style="width: 25%;" class="pl-1">স্বাস্থ্যসেবা প্রতিষ্ঠানের পূর্ণনাম</td>
                      <td colspan="4">
                        <input type="text" v-model="form.company_full_name" class="form-control first_block">
                      </td>
                      <td style="width: 7%;"></td>
                    </tr>
                    <tr>
                      <td class="parent_table_serial text-center">০২ </td>
                      <td class="pl-1">প্রতিষ্ঠান প্রধানের পদবী </td>
                      <td colspan="4">
                        <input type="text" v-model="form.designation_of_head_of_company" class="form-control first_block">
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="parent_table_serial text-center">০৩ </td>
                      <td class="pl-1">প্রতিষ্ঠান প্রধানের নাম</td>
                      <td colspan="4">
                        <input type="text" v-model="form.name_of_head_of_company" class="form-control first_block">
                      </td>
                      <td w></td>
                    </tr>
                    <tr>
                      <td class="text-center">০৪</td>
                      <td class="pl-1">প্রতিষ্ঠানের অবস্থান</td>
                      <td colspan="4" class="pl-1">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="">
                              <label for="division_id">বিভাগের নাম</label>
                              <ResourceSelect
                                  v-model="form.division_id"
                                  bUrl="niport/division"
                                  :clearable="false"
                                  :isEdit="form.id?true:false"
                                  :labelField="(item) => `${item.bn_name}`"
                                />
                              
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="">
                              <label for="district_id">জেলার নাম</label>
                              <vSelect
                                v-model="form.district_id"
                                :options="districts"
                                :reduce="district => district.id"
                                label="bn_name"
                                :disabled="!form.division_id"
                                placeholder="Select District"

                              />
                            
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="">
                              <label for="upazila_id">উপজেলার নাম</label>
                              <vSelect
                                v-model="form.upazila_id"
                                :options="upazilas"
                                label="bn_name"
                                :reduce="item => item.id"
                                :disabled="!form.district_id"
                                placeholder="Select Upazila"
                              />
                              
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="">
                              <label for="union_id">ইউনিয়নের নাম</label>
                              <vSelect
                                v-model="form.union_id"
                                :options="unions"
                                label="bn_name"
                                :reduce="item => item.id"
                                :disabled="!form.upazila_id"
                                placeholder="Select union"
                              />
                              

                            </div>
                          </div>
                        </div>

                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="text-center">০৫</td>
                      <td class="pl-1">অক্ষাংশ-দ্রাঘিমাংশ</td>
                      <td colspan="2" class="pl-1">
                        <label for="">অক্ষাংশ</label>
                        <input type="text" v-model="form.latitude" class="form-control first_block">
                      </td>
                      <td colspan="2" class="pl-1">
                        <label for="">দ্রাঘিমাংশ</label>
                        <input type="text" v-model="form.longitude" class="form-control first_block">
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="text-center">০৬</td>
                      <td class="pl-1">মহল্লা/ওয়ার্ড নম্বর/রাস্তার নাম </td>
                      <td colspan="4" class="p-0">
                        <table class="internal-table w-100  table table-bordered mb-0">
                          <tr>
                            <td style="width: 50%; border-right: 1px solid #dee2e6;">মহল্লার নাম</td>
                            <td><input type="text" class="form-control first_block" v-model="form.moholla_name""></td>
                          </tr>
                          <tr>
                            <td style="border-right: 1px solid #dee2e6;">ওয়ার্ড নম্বর</td>
                            <td><input type="text" class="form-control first_block" v-model="form.word_no"></td>
                          </tr>
                          <tr>
                            <td style="border-right: 1px solid #dee2e6;">রাস্তার নাম</td>
                            <td><input type="text" class="form-control first_block" v-model="form.road_no"></td>
                          </tr>
                          <tr>
                            <td style="border-right: 1px solid #dee2e6;">হোল্ডিং নম্বর</td>
                            <td><input type="text" class="form-control first_block" v-model="form.holding_no"></td>
                          </tr>
                        </table>
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td class="text-center">০৭</td>
                      <td class="pl-1">প্রতিষ্ঠানের সাথে যোগাযোগের জন্য
                        কনট্রাক্ট নম্বর </td>
                          <td colspan="4" class="p-0" >
                            <table class="internal-table table table-bordered w-100 mb-0">
                              <tbody>
                                <tr>
                                  <td style="width: 50%;" class="pl-1">ল্যান্ড ফোন নম্বর</td>
                                  <td><input type="text" class="form-control first_block" v-model="form.land_phone_no"  value=""></td>
                                </tr>
                                <tr>
                                  <td class="pl-1">সেল ফোন নম্বর</td>
                                  <td><input type="text" class="form-control first_block" v-model="form.cell_phone_no"   value=""></td>
                                </tr>
                                <tr>
                                  <td class="pl-1">ই-মেইল নম্বর </td>
                                  <td><input type="text" class="form-control first_block" v-model="form.email_no"  value=""></td>
                                </tr>
                                <tr>
                                  <td class="pl-1">ওয়েব এড্রেস</td>
                                  <td><input type="text" class="form-control first_block"  v-model="form.web_address" value=""></td>
                                </tr>
                                <tr>
                                  <td class="pl-1">অন্যান্য</td>
                                  <td><input type="text" class="form-control first_block" v-model="form.contact_no_others"  value="">
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                          <td></td>
                        </tr>
                        <tr>
                        <td class="text-center">০৮</td>
                        <td class="pl-1">প্রতিষ্ঠানের আইডি নম্বর (যদি থাকে)</td>
                        <td colspan="4"><input type="text" class="form-control first_block" v-model="form.id_no_of_company"   value=""></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center">০৯</td>
                        <td class="pl-1">প্রতিষ্ঠানের জিআইএস নম্বর (যদি থাকে) </td>
                        <td colspan="4"><input type="text" class="form-control first_block"  v-model="form.gis_no_of_company"  value=""></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center">১০</td>
                        <td class="pl-1">প্রতিষ্ঠানের প্রতিষ্ঠাকাল </td>
                        <td colspan="4"><input type="text" class="form-control first_block"  v-model="form.founding_period_of_company" value=""></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center">১১</td>
                        <td class="pl-1">প্রতিষ্ঠানের পূর্বের নাম (যদি থাকে) </td>
                        <td colspan="4"><input type="text" class="form-control first_block"  v-model="form.previous_name_of_company" value=""></td>
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
                        <td class="text-center">১২</td>
                        <td class="pl-1">প্রতিষ্ঠানের রেজিষ্ট্রেশন নম্বর (প্রযোজ্য ক্ষেত্রে)</td>
                        <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.registration_no_of_company" value=""></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center">১৩</td>
                        <td class="pl-1">প্রতিষ্ঠানের লাইসেন্স নম্বর ও ঠিকানা (প্রযোজ্য ক্ষেত্রে)</td>
                        <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.license_no_of_company" value=""></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td class="text-center">১৪</td>
                        <td class="pl-1">প্রতিষ্ঠানের লাইসেন্সের মেয়াদকাল (প্রযোজ্য ক্ষেত্রে)</td>
                        <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.expiration_date_license_of_company" value="">
                        </td>
                        <td></td>
                      </tr>
                      <tr v-if="!isUnion()">
                        <td class="text-center">১৫</td>
                        <td class="pl-1">প্রতিষ্ঠানের সর্বমোট বেডের সংখ্যা</td>
                        <td colspan="4" class="p-0">
                          <table class="internal-table w-100 table table-bordered mb-0">
                            <tr>
                              <td style="width: 50%; border-right: 1px solid #dee2e6;">ওয়ার্ডঃ</td>
                              <td><input type="text" class="form-control second_block from_broder"  v-model="form.no_of_bed_ward" value=""></td>
                            </tr>
                            <tr>
                              <td style="border-right: 1px solid #dee2e6;">কেবিনঃ</td>
                              <td><input type="text" class="form-control second_block from_broder"  v-model="form.no_of_bed_cabin" value=""></td>
                            </tr>
                          </table>
                        </td>
                        <td></td>
                      </tr>
                      <tr>
                          <td class="text-center">{{isUnion()?'১৫':'১৬'}}</td>
                          <td class="pl-1">প্রতিষ্ঠানটি কীভাবে পরিচালিত হচ্ছে?</td>
                          <td colspan="4" class="p-0">
                            <table class="internal-table w-100 table table-bordered mb-0">
                            
                                <tr  v-for="(value, index) in runStategy()" :key="index">
                                  <td style="width: 50%; border-right: 1px solid #dee2e6;">{{ value }}  </td>
                                  <td>
                                    <div class="form-check-inline">
                                      <input class="form-check-input second_block"  type="radio" v-model="form.run_stategy" name="run_stategy" :value="index"   @change="form.run_stategy != 4 && (form.run_stategy_others = '')">
                                    </div>
                                
                                
                                      <input type="text" v-if="index==4" class="form-control from_broder"  v-model="form.run_stategy_others" :readonly="form.run_stategy != 4"  >
                                      
                                
                                  </td>
                                </tr>
                              
                            </table>
                          </td>
                          <td></td>
                        </tr>
                        <tr>
                          <td class="text-center">{{isUnion()?'১৬':'১৭'}}</td>
                          <td class="pl-1">সাক্ষাৎকার প্রদানকারীর নাম </td>
                          <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.name_of_interview_provider" value=""></td>
                          <td></td>
                        </tr>
                        <tr>
                          <td class="text-center">{{isUnion()?'১৭':'১৮'}}</td>
                          <td class="pl-1">সাক্ষাৎকার প্রদানকারীর পদবী</td>
                          <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.designation_of_interview_provider" value="">
                          </td>
                          <td></td>
                        </tr>
                        <tr>
                          <td class="text-center">{{isUnion()?'১৮':'১৯'}}</td>
                          <td class="pl-1">সাক্ষাৎকার প্রদানকারীর মোবাইল নম্বর </td>
                          <td colspan="4"><input type="text" class="form-control second_block"  v-model="form.mobile_of_interview_provider" value=""></td>
                          <td></td>
                        </tr>
                            
                              <IntroductionUnion
                                v-show="isUnion()"
                                :faculty_id="faculty_id"
                                :category_id="category_id"
                              />

                              <Introduction
                                v-show="!isUnion()"
                                :faculty_id="faculty_id"
                                :category_id="category_id"
                              />

                                <tr>
                        <td class="text-center">{{isUnion()?'২৫':'৩২'}}</td>
                        <td colspan="2" class="pl-1">হাসপাতালের মালিকানার ধরন?</td>
                        <td colspan="3" class="p-0">
                          <table class="table table-bordered mb-0">
                              <tr  v-for="(value, index) in typesOfOwnership()" :key="index">
                                <td class="w-50" style="border-right: 1px solid #dee2e6;">{{ value }}</td>
                                <td class="align-middle" >
                                  <div class=" form-check-inline">
                                      <input class="form-check-input ninth_block"  type="radio"  v-model="form.types_of_ownership" name="types_of_ownership" :value=" index">
                                    </div>
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
                      <tr>
                        <td rowspan="2" class="text-center">{{isUnion()?'২৬':'৩৩'}}</td>
                        <td colspan="5" class="pl-1">হাসপাতালের সার্বিক অবস্থা</td>
                        <td rowspan="2"></td>
                      </tr>
                      <tr>
                        <td colspan="5" class="p-0">
                          <table class="table table-border mb-0 ">
                            <tr>
                              <td class="text-center from_broder" width="40">নং</td>
                              <td class="from_broder text-center">বিবরন</td>
                              <td class="from_broder text-center">পর্যাপ্ত</td>
                              <td class="from_broder text-center">অপর্যাপ্ত</td>
                              <td class="from_broder text-center">মন্তব্য</td>
                            </tr>
                            <tr v-for="condition in conditions" :key="condition.id">
                              <td class="from_broder text-center">{{ condition.serial_no }}</td>
                              <td class="from_broder">{{ condition.condition_name }}
                                
                              </td>
                              <td class="from_broder text-center">
                                  <div class="checkbox-wrapper">
                                    <label>
                                      <input type="checkbox" class="" v-model="getConditions(condition.id).sufficient"   :name="`sufficient_${condition.id}`">
                                      <span class="checkbox"></span>
                                    </label>
                                </div>
                                
                              </td>
                              <td class="from_broder text-center">
                                <div class="checkbox-wrapper">
                                    <label>
                                      <input type="checkbox" class="" v-model="getConditions(condition.id).insufficient"   :name="`insufficient_${condition.id}`">
                                      <span class="checkbox"></span>
                                    </label>
                                </div>
                                
                              </td>
                              <td class="from_broder">
                                <input type="text" class="form-control tenth_block" v-model="getConditions(condition.id).comments"  value="">
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="7" class="text-end">
                          <LoadingButton :loading="isSubmitting" class="d-inline-block" size="sm" variant="primary" @click="formSubmit" >
                            <span v-if="!form.isSubmit">Update</span>
                            <span v-else>Submit</span>
                          </LoadingButton>
                        </td>
                      </tr>

                    </tbody>
                </table>

              </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার প্রদানকারীর নাম:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.name_of_interview_provider">
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার প্রদানকারীর পদবী:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.interview_provider_id" >
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার প্রদানকারীর মোবাইল:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.mobile_of_interview_provider" >
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> তারিখ:</label>
                        <DatePicker v-model="form.interview_provider_date" />
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার গ্রহণকার নাম:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.interview_receipient_name">
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার গ্রহণকার পদবী:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.interview_receipient_id">
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> সাক্ষাৎকার গ্রহণকার মোবাইল:</label>
                        <input type="text" class="form-control eleventh_block" v-model="form.interview_receipient_mobile" >
                      </div>
                      <div class="input-group mb-2">
                        <label class="col-sm-12"> তারিখ:</label>
                        <DatePicker v-model="form.interview_receipient_date" />
                      </div>
                    </div>
                  </div>
                
                  <p class="text-center">আপনার মূল্যবান সময় ও সাক্ষাৎকারটি দেওয়ার জন্য আন্তরিক ধন্যবাদ</p>
              </template>
              <div class="text-end" v-show="!isSpinner">
                    <LoadingButton :loading="isSubmitting" class="d-inline-block" size="sm" variant="primary" @click="formSubmit" >
                      <span v-if="!form.isSubmit">Update</span>
                      <span v-else>Submit</span>
                    </LoadingButton>
                  </div>
            <!-- </template> -->
        </BCardText>

      </BCard>
    </div>
  </div>



</template>
<style scoped>
.v-select, .v-select *{
  background-color: #fff;
}

  .questionnaire {
      padding: 2px 12px;
      border: 1px solid #000;
  }
  .instrument_category, .study_title, .respondent, .company_address, .conducted_by {
    font-size: 24px;
    font-weight: bolder;
}

.page {
      background: #fff;
      padding: 20px;
      /* max-width: 1000px; */
      margin: auto;
      border: 1px solid #ddd;
    }
    .title {
      font-weight: 600;
      text-align: center;
    }

    .small-text {
      font-size: 14px;
    }
    .logo {
      width: 80px;
    }
    .sl-box span {
      display: inline-block;
      width: 25px;
      height: 25px;
      border: 1px solid #000;
      margin-left: 3px;
    }



  .option-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    -ms-appearance: none;
    -o-appearance: none;
    appearance: none;
    position: relative;
    top: 13.33333px;
    right: 0;
    bottom: 0;
    left: 0;
    height: 40px;
    width: 40px;
    transition: all 0.15s ease-out 0s;
    background: #cbd1d8;
    border: none;
    color: #fff;
    cursor: pointer;
    display: inline-block;
    margin-right: 0.5rem;
    outline: none;
    position: relative;
  z-index: 1000;
}
.option-input:hover {
  background: #9faab7;
}
.option-input:checked {
  background: #40e0d0;
}
.option-input:checked::before {
  width: 40px;
  height: 40px;
  display:flex;
  content: '\f00c';
  font-size: 25px;
  font-weight:bold;
  position: absolute;
  align-items:center;
  justify-content:center;
  font-family:'Font Awesome 5 Free';
}
.option-input:checked::after {
  -webkit-animation: click-wave 0.65s;
  -moz-animation: click-wave 0.65s;
  animation: click-wave 0.65s;
  background: #40e0d0;
  content: '';
  display: block;
  position: relative;
  z-index: 100;
}
.option-input.radio {
  border-radius: 50%;
}
.option-input.radio::after {
  border-radius: 50%;
}

@keyframes click-wave {
  0% {
    height: 40px;
    width: 40px;
    opacity: 0.35;
    position: relative;
  }
  100% {
    height: 200px;
    width: 200px;
    margin-left: -80px;
    margin-top: -80px;
    opacity: 0;
  }
}


td{
  padding: 0px 10px;
  vertical-align: middle;
  font-size: 13px;
  background: #f9f9f9;
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

  .form-control:not(textarea), .custom-file-label, .custom-file-label::after, .custom-file, .custom_text_area, .form-select {
    height: 25px !important;
    padding: 0 12px !important;
}

.from_broder, input:focus, .from_broder:focus{
  border: 1px solid #dee2e6 !important;
}

</style>