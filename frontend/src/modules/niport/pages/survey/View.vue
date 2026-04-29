<script setup>
import { ref, computed, onMounted, provide, reactive,watch    } from 'vue'
import { useRoute, useRouter  } from "vue-router";
import { useResourceApiClient } from '@/composables/resourceApiClient'
import {printADiv, typesOfOwnership,runStategy, mediaOfCompanyService  } from '@/utilities/methods'
import * as XLSX from "xlsx"

const route = useRoute();
const router = useRouter()
const view_id   = computed(() => route.params.view_id);

//  Setup
const title = 'Survey Information'
const bUrl =`niport/surveys`;

const {
  getOne,
  customGet,
  isSpinner
} = useResourceApiClient(bUrl, title)



const objData = ref('')

onMounted(async () => {
   try {
    objData.value = await getOne(view_id.value)
   }catch (error) {
      router.replace('/niport/404')
   }
  
})


const exportFullHtml = () => {
  const element = document.getElementById("dataTablePrint")

  const html = element.outerHTML

  const blob = new Blob([html], {
    type: "application/vnd.ms-excel;charset=utf-8;"
  })

  const url = URL.createObjectURL(blob)

  const a = document.createElement("a")
  a.href = url
  a.download = "full-data.xls"
  a.click()

  URL.revokeObjectURL(url)
}


</script>

<template>
  <CenteredSpinner v-if="isSpinner"  />
  <div class="px-3" v-show="!isSpinner">
    <div class="mx-auto w-100 " >
      <BCard  header-tag="header" >
        <template #header>
          <h2 class="card-title fw-bold " >
            <!-- <i class="fas fa-eye"></i> {{ title }} -->
          </h2>
          <div class="card-tools">
             <BButton  variant="primary" title="Export to Excel" size="sm" @click="exportFullHtml()">
                <i class="fas fa-download"></i> Export
              </BButton> &nbsp;
              <BButton  variant="warning" size="sm" @click="printADiv('dataTablePrint')">
                <i class="fas fa-print"></i> Print
              </BButton> &nbsp;
              <RouterLink class="btn btn-info btn-sm" :to="`/niport/survey/${objData?.category?.type}`"><i class="fas fa-list"></i> Survey List</RouterLink>
            </div>
        </template>
        <BCardText >
          <div id="dataTablePrint" class="table-responsive">
           <h3 class="text-center text-underline1">জাতীয় জনসংখ্যা গবেষণা ও প্রশিক্ষণ ইনস্টিটিউট (নিপোর্ট)</h3>
            <div id="session-block11">
               <table class="table table-bordered parent-table">
                <tbody>
                  <tr>
                    <td colspan="7" class="text-end">কোড নং</td>
                  </tr>
                  <tr v-if="objData.questionnaire_id">
                    <td colspan="6" ><span style="color: blue;">Questionnaire ID : </span>
                     {{ objData.questionnaire_id }}
                    </td>
                    <td style="width: 7%;"></td>
                  </tr>
                  <tr v-if="objData.company_full_name">
                    <td width="50" class="text-center">০১ </td>
                    <td  style="width: 25%;" >স্বাস্থ্যসেবা প্রতিষ্ঠানের পূর্ণনাম</td>
                    <td colspan="4">{{ objData.company_full_name}}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.designation_of_head_of_company">
                    <td class="text-center">০২ </td>
                    <td>প্রতিষ্ঠান প্রধানের পদবী </td>
                    <td colspan="4">{{ objData.designation_of_head_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.name_of_head_of_company">
                    <td class="text-center">০৩ </td>
                    <td>প্রতিষ্ঠান প্রধানের নাম</td>
                    <td colspan="4">{{ objData.name_of_head_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.division_id">
                    <td class="text-center">০৪</td>
                    <td>প্রতিষ্ঠানের অবস্থান</td>
                    <td colspan="4">
                      <div class="row">
                        <div class="col-md-3">
                          বিভাগের নাম : {{ objData.division.bn_name??'' }}
                        </div>
                        <div class="col-md-3">
                          জেলার নাম : {{ objData.district.bn_name??'' }}
                        </div>
                        <div class="col-md-3">
                          উপজেলার নাম : {{ objData.upazila.bn_name??'' }}
                        </div>
                        <div class="col-md-3">
                          ইউনিয়নের নাম : {{ objData.union.bn_name??'' }}
                        </div>
                      </div>
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.latitude || objData.longitude">
                    <td class="text-center">০৫</td>
                    <td>অক্ষাংশ-দ্রাঘিমাংশ</td>
                    <td colspan="2">
                      <label for="">অক্ষাংশ : {{ objData.latitude }} </label>
                    </td>
                    <td colspan="2">
                      <label for="">দ্রাঘিমাংশ : {{ objData.longitude }}</label>
                    </td>
                    <td></td>
                  </tr>

                   <tr v-if="objData.moholla_name || objData.word_no || objData.road_no || objData.holding_no">
                    <td class="text-center">০৬</td>
                    <td>মহল্লা/ওয়ার্ড নম্বর/রাস্তার নাম </td>
                    <td colspan="4" style="padding-left: 0px !important; padding-right: 0px !important;">
                      <table class="table table-bordered mb-0">
                        <tr>
                          <td style="width: 50%; border-right: 1px solid #dee2e6;">মহল্লার নাম</td>
                          <td>{{ objData.moholla_name }}</td>
                        </tr>
                        <tr>
                          <td style="width: 50%; border-right: 1px solid #dee2e6;">ওয়ার্ড নম্বর</td>
                          <td>{{ objData.word_no }}</td>
                        </tr>
                        <tr>
                          <td style="width: 50%; border-right: 1px solid #dee2e6;">রাস্তার নাম</td>
                          <td>{{ objData.road_no  }}</td>
                        </tr>
                        <tr>
                          <td style="width: 50%; border-right: 1px solid #dee2e6;">হোল্ডিং নম্বর</td>
                          <td>{{ objData.holding_no }}</td>
                        </tr>
                      </table>
                    </td>
                    <td></td>
                  </tr>

                  <tr v-if="objData.land_phone_no || objData.cell_phone_no ||  objData.email_no || objData.web_address || objData.contact_no_others">
                    <td class="text-center">০৭</td>
                    <td>প্রতিষ্ঠানের সাথে যোগাযোগের জন্য কনট্রাক্ট নম্বর </td>
                    <td colspan="4" style="padding-left: 0px !important; padding-right: 0px !important;">
                      <table class="table table-bordered mb-0">
                        <tbody>
                          <tr>
                            <td>ল্যান্ড ফোন নম্বর</td>
                            <td>{{ objData.land_phone_no }}</td>
                          </tr>
                          <tr>
                            <td>সেল ফোন নম্বর</td>
                            <td>{{ objData.cell_phone_no }}</td>
                          </tr>
                          <tr>
                            <td>ই-মেইল নম্বর </td>
                            <td>{{ objData.email_no }}</td>
                          </tr>
                          <tr>
                            <td>ওয়েব এড্রেস</td>
                            <td>{{ objData.web_address }}</td>
                          </tr>
                          <tr>
                            <td>অন্যান্য</td>
                            <td>{{ objData.contact_no_others }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.id_no_of_company">
                    <td class="text-center">০৮</td>
                    <td>প্রতিষ্ঠানের আইডি নম্বর (যদি থাকে)</td>
                    <td colspan="4">{{ objData.id_no_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.gis_no_of_company">
                    <td class="text-center">০৯</td>
                    <td>প্রতিষ্ঠানের জিআইএস নম্বর (যদি থাকে) </td>
                    <td colspan="4">{{ objData.gis_no_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.founding_period_of_company">
                    <td class="text-center">১০</td>
                    <td>প্রতিষ্ঠানের প্রতিষ্ঠাকাল </td>
                    <td colspan="4">{{ objData.founding_period_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.previous_name_of_company">
                    <td class="text-center">১১</td>
                    <td>প্রতিষ্ঠানের পূর্বের নাম (যদি থাকে) </td>
                    <td colspan="4">{{ objData.previous_name_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.registration_no_of_company">
                    <td class="text-center">১২</td>
                    <td>প্রতিষ্ঠানের রেজিষ্ট্রেশন নম্বর (প্রযোজ্য ক্ষেত্রে)</td>
                    <td colspan="4">{{ objData.registration_no_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.license_no_of_company">
                    <td class="text-center">১৩</td>
                    <td>প্রতিষ্ঠানের লাইসেন্স নম্বর ও ঠিকানা (প্রযোজ্য ক্ষেত্রে)</td>
                    <td colspan="4">{{ objData.license_no_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.expiration_date_license_of_company">
                    <td class="text-center">১৪</td>
                    <td>প্রতিষ্ঠানের লাইসেন্সের মেয়াদকাল (প্রযোজ্য ক্ষেত্রে)</td>
                    <td colspan="4">{{ objData.expiration_date_license_of_company }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.no_of_bed_ward || objData.no_of_bed_cabin">
                    <td class="text-center">১৫</td>
                    <td>প্রতিষ্ঠানের বেডের সংখ্যা</td>
                    <td colspan="4" style="padding-left: 0px !important; padding-right: 0px !important;">
                      <table class="table table-bordered mb-0">
                        <tr>
                          <td width="50%" style="border-right: 1px solid #dee2e6;">ওয়ার্ডঃ</td>
                          <td>{{ objData.no_of_bed_ward }}</td>
                        </tr>
                        <tr>
                          <td style="border-right: 1px solid #dee2e6">কেবিনঃ</td>
                          <td>{{ objData.no_of_bed_cabin }}</td>
                        </tr>
                      </table>
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.run_stategy || objData.run_stategy_others">
                    <td class="text-center">১৬</td>
                    <td>প্রতিষ্ঠানটি কীভাবে পরিচালিত হচ্ছে?</td>
                    <td colspan="4">
                       <template v-if="objData.run_stategy != 4">
                              {{ runStategy()[objData.run_stategy]??'' }} 
                          </template>
                        <template v-else>{{  objData.run_stategy_others }}</template>
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.name_of_interview_provider">
                    <td class="text-center">১৭</td>
                    <td>সাক্ষাৎকার প্রদানকারীর নাম </td>
                    <td colspan="4">{{ objData.name_of_interview_provider }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.designation_of_interview_provider">
                    <td class="text-center">১৮</td>
                    <td>সাক্ষাৎকার প্রদানকারীর পদবী</td>
                    <td colspan="4">{{ objData.designation_of_interview_provider }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.mobile_of_interview_provider">
                    <td class="text-center">১৯</td>
                    <td>সাক্ষাৎকার প্রদানকারীর মোবাইল নম্বর </td>
                    <td colspan="4">{{ objData.mobile_of_interview_provider }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.media_of_company_service">
                    <td class="text-center">২০</td>
                    <td>প্রতিষ্ঠানের সেবা প্রদানের মাধ্যম</td>
                    <td colspan="4" style="padding-left: 0px !important; padding-right: 0px !important;">
                      <table class="table table-bordered mb-0">
                          <tr  v-for="(value, index) in objData.media_of_company_service" :key="index">
                            <td width="40%" style="border-right: 1px solid #dee2e6;">{{ mediaOfCompanyService()[value]??'' }}</td>
                            <td>
                              <template v-if="value ==5">{{ objData.media_of_company_service_others }}</template>
                            </td>
                          </tr>
                      </table>
                    </td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.total_patient_preivious_month_outdoor_service">
                    <td class="text-center">২১</td>
                    <td colspan="3">আউটডোর হতে গত এক মাসে কতজন রোগি সেবা নিয়েছেন?</td>
                    <td colspan="2">{{ objData.total_patient_preivious_month_outdoor_service }}</td>
                    <td></td>
                  </tr>
                  <tr v-if="objData.total_patient_preivious_month_outdoor_service_hospital_adm">
                    <td class="text-center">২২</td>
                    <td colspan="3">আউটডোর হতে সেবা নিয়ে গত এক মাসে কতজন রোগি হাসপাতালে ভর্তি হয়েছেন? </td>
                    <td colspan="2">{{ objData.total_patient_preivious_month_outdoor_service_hospital_adm }}</td>
                    <td></td>
                  </tr>
                  <template v-if="objData.out_door_departments">
                    <tr>
                      <td rowspan="2" class="text-center">২৩</td>
                      <td colspan="5">আউটডোরে কী কী ইউনিট/বিভাগ আছে? </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td colspan="5">ইউনিট/বিভাগসমূহ </td>
                      <td class="text-center">কোড</td>
                    </tr>
                    <tr v-for="(value, index) in objData.out_door_departments" :key="index">
                      <td class="text-center">{{ value.department.serial_no??'' }}</td>
                      <td colspan="5">{{ value.department.department_name??'' }}</td>
                      
                      <td class="text-center"> {{ value.department.code_no??'' }}</td>
                    </tr>
                  </template>
                  <template v-if="objData.outDoorDepartmentStaffs">
                    <tr>
                        <td class="text-center">২৪</td>
                        <td colspan="6">আউটডোরে কোন্ ইউনিট/বিভাগে কতজন ষ্টাফ কর্মরত আছেন? </td>
                      </tr>
                      <tr>
                        <td></td>
                        <td>ইউনিট/বিভাগসমূহ </td>
                        <td width="18%">কর্মরত ষ্টাফের পদবী</td>
                        <td colspan="3" class="text-center">কর্মরত ষ্টাফের সংখ্যা</td>
                        <td></td>
                      </tr>
                      <tr  v-for="(value, index) in objData.outDoorDepartmentStaffs" :key="index">
                        <td class="text-center">{{ value.department.serial_no??'' }}</td>
                        <td>{{ value.department.department_name??'' }}</td>
                        <td colspan="4"  style="padding-left: 0px !important; padding-right: 0px !important;">
                          <table class="table table-bordered mb-0">
                            <tr>
                              <td style="border-right: 1px solid #dee2e6;">&nbsp;</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">নিজস্ব</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">গেষ্ট ডক্টর</td>
                              <td class="text-center">কনসালটেন্ট</td>
                            </tr>
                            <tr v-for="(value1, index1) in value.staffs" :key="index1">
                              <td style="border-right: 1px solid #dee2e6;">{{ value1.designation.designation_name??'' }}</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">{{ value1.self_doctor }}</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">{{ value1.guest_doctor }}</td>
                              <td class="text-center">{{ value1.consultant }}</td>
                            </tr>
                          </table>
                        </td>
                       
                        <td class="text-center"> {{ value.department.code_no??'' }}</td>
                      </tr>
                    </template>
                    <tr v-if="objData.total_patient_preivious_month_indoor_service">
                      <td class="text-center">২৫</td>
                      <td colspan="3">ইনডোর হতে গত এক মাসে কতজন রোগি সেবা নিয়েছেন?</td>
                      <td colspan="2">{{ objData.total_patient_preivious_month_indoor_service }}</td>
                      <td></td>
                    </tr>
                    <tr v-if="objData.total_patient_addmitted_indoor_service">
                      <td class="text-center">২৬</td>
                      <td colspan="3">ইনডোরে বর্তমানে কতজন রোগি ভর্তি আছেন?</td>
                      <td colspan="2">{{ objData.total_patient_addmitted_indoor_service }}</td>
                      <td></td>
                    </tr>
                    <template v-if="objData.in_door_departments">
                      <tr>
                        <td rowspan="2" class="text-center">২৭</td>
                        <td colspan="5">ইনডোরে কী কী ইউনিট/বিভাগ আছে?</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="5">ইউনিট/বিভাগসমূহ</td>
                        <td class="text-center">কোড</td>
                      </tr>
                      <tr v-for="(value, index) in objData.in_door_departments" :key="index">
                        <td class="text-center">{{ value.department.serial_no??'' }}</td>
                        <td colspan="5">{{ value.department.department_name??'' }}</td>
                        
                        <td class="text-center"> {{ value.department.code_no??'' }}</td>
                      </tr>
                    </template>

                    <template v-if="objData.inDoorDepartmentStaffs">
                      <tr>
                        <td class="text-center">২৪</td>
                        <td colspan="6">আউটডোরে কোন্ ইউনিট/বিভাগে কতজন ষ্টাফ কর্মরত আছেন? </td>
                      </tr>
                      <tr>
                        <td></td>
                        <td>ইউনিট/বিভাগসমূহ </td>
                        <td width="18%">কর্মরত ষ্টাফের পদবী</td>
                        <td colspan="3" class="text-center">কর্মরত ষ্টাফের সংখ্যা</td>
                        <td></td>
                      </tr>
                      <tr  v-for="(value, index) in objData.inDoorDepartmentStaffs" :key="index">
                        <td class="text-center">{{ value.department.serial_no??'' }}</td>
                        <td>{{ value.department.department_name??'' }}</td>
                        <td colspan="4"  style="padding-left: 0px !important; padding-right: 0px !important;">
                          <table class="table table-bordered mb-0">
                            <tr>
                              <td style="border-right: 1px solid #dee2e6;">&nbsp;</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">নিজস্ব</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">গেষ্ট ডক্টর</td>
                              <td class="text-center">কনসালটেন্ট</td>
                            </tr>
                            <tr v-for="(value1, index1) in value.staffs" :key="index1">
                              <td style="border-right: 1px solid #dee2e6;">{{ value1.designation.designation_name??'' }}</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">{{ value1.self_doctor }}</td>
                              <td class="text-center" style="border-right: 1px solid #dee2e6;">{{ value1.guest_doctor }}</td>
                              <td class="text-center">{{ value1.consultant }}</td>
                            </tr>
                          </table>
                        </td>
                        <td class="text-center"> {{ value.department.code_no??'' }}</td>
                      </tr>
                    </template>
                    <template v-if="objData.departmentTests">
                      <tr>
                        <td class="text-center">২৯</td>
                        <td colspan="6">এই হাসপাতালে ডায়াগনষ্টিক কী কী ইউনিট/বিভাগ আছে এবং কোন্ ইউনিট/বিভাগ হতে
                      কী কী পরীক্ষা/টেষ্ট করা হয়? </td>
                      </tr>
                      <tr>
                        <td colspan="2">ইউনিট/বিভাগসমূহ</td>
                        <td colspan="4">পরীক্ষা/টেষ্টসমূহ</td>
                        <td class="text-center">কোড</td>
                      </tr>
                      <tr  v-for="(value, index) in objData.departmentTests" :key="index">
                        <td class="text-center">{{ value.department.serial_no??'' }}</td>
                        <td>{{ value.department.department_name??'' }}</td>
                        <td colspan="5"  style="padding-left: 0px !important; padding-right: 0px !important;">
                          <table class="table table-bordered mb-0">
                            
                            <tr v-for="(value1, index1) in value.tests" :key="index1">
                              <td style="border-right: 1px solid #dee2e6;">{{ value1.test_name }}
                                <template v-if="value1.test_name === 'অন্যান্য (Others)' && value1.other_test">
                                  <br>{{ value1.other_test }}
                                </template>
                              </td>
                              <td class="text-center" width="110"> {{ value1.code_no??'' }}</td>
                            </tr>
                          </table>
                        </td>
                        
                      </tr>
                    </template>
                    <template v-if="objData.department_consultations">
                      <tr>
                        <td rowspan="2" class="text-center">৩০</td>
                        <td colspan="6">এই হাসপাতালে কনসালটেশনের জন্য কোন্ কোন্ ইউনিট/বিভাগে কতজন কনসালটেন্ট
                      বসেন?</td>
                      </tr>
                      <tr>
                        <td colspan="4"  class="text-center">ইউনিট/বিভাগসমূহ</td>
                        <td class="text-center">কনসালটেন্টের সংখ্যা</td>
                        <td class="text-center">কোড</td>
                      </tr>
                      <tr  v-for="(value, index) in objData.department_consultations" :key="index">
                        <td class="text-center">{{ value.department.serial_no??'' }}</td>
                        <td colspan="4">{{ value.department.department_name??'' }}</td>
                        <td class="text-center">{{ value.total_consultation??'' }}</td>
                        <td class="text-center">{{ value.department.code_no??'' }}</td>
                      </tr>
                    </template>
                    <template v-if="objData.diseases">
                      <tr>
                        <td :rowspan="objData.diseases.length+1" class="text-center">৩১</td>
                        <td colspan="5">এই হাসপাতালে নিম্নের কোন্ কোন্ রোগের চিকিৎসা হয়?</td>
                        <td></td>
                      </tr>
                      <tr  v-for="(value, index) in objData.diseases" :key="index">
                        <td colspan="4">{{ value.disease.diseases_name??'' }}
                          <p v-if="value.disease?.diseases_name=='অন্যান্য'">{{ value.diseases_others }}</p>
                        </td>
                        <td></td>
                        <td class="text-center">{{ value.disease.code_no??'' }}</td>
                      </tr>
                    </template>
                    <tr v-if="objData.types_of_ownership">
                      <td class="text-center">৩২</td>
                      <td colspan="2">হাসপাতালের মালিকানার ধরন?</td>
                      <td colspan="3">{{ typesOfOwnership()[objData.types_of_ownership]??'' }}</td>
                      <td></td>
                    </tr>
                    
                      <tr v-if="objData.conditions">
                        <td rowspan="2" class="text-center">৩৩</td>
                        <td colspan="5" style="padding-left: 0px !important; padding-right: 0px !important;">
                          
                          <table class="table table-bordered mb-0">
                            <tr>
                              <td colspan="5">হাসপাতালের সার্বিক অবস্থা</td>
                            </tr>
                            <tr>
                              <td style="border-right: 1px solid #dee2e6;" class="text-center" width="50">নং</td>
                              <td style="border-right: 1px solid #dee2e6;">বিবরন</td>
                              <td style="border-right: 1px solid #dee2e6;" class="text-center">পর্যাপ্ত</td>
                              <td style="border-right: 1px solid #dee2e6;" class="text-center">অপর্যাপ্ত</td>
                              <td style="border-right: 1px solid #dee2e6;">মন্তব্য</td>
                            </tr>
                             <tr  v-for="(value, index) in objData.conditions" :key="index">
                              <td style="border-right: 1px solid #dee2e6;" class="text-center">{{ value?.condition?.serial_no }}</td>
                              <td style="border-right: 1px solid #dee2e6;">{{ value?.condition?.condition_name}}</td>
                              <td style="border-right: 1px solid #dee2e6;" class="text-center"><i v-if="value.sufficient" class="fas fa-check-square"></i></td>
                              <td style="border-right: 1px solid #dee2e6;" class="text-center"><i v-if="value.insufficient" class="fas fa-check-square"></i></td>
                              <td style="border-right: 1px solid #dee2e6;">{{ value.comments }}</td>
                            </tr>

                          </table>
                        </td>
                        <td></td>
                      </tr>
                </tbody>
               </table>
               <table style="width: 100%;">
                <tbody>
                  <tr>
                    <td style="padding: 0!important;">
                  <br>
                  <table class="signature-table">
                
                    <tr v-if="objData.name_of_interview_provider">
                      <td style="width:50%;text-align:right!important;">সাক্ষাৎকার প্রদানকারীর নাম</td>
                      <td style="text-align:left!important;"> : {{ objData.name_of_interview_provider }}</td>
                    </tr>
                
                    <tr v-if="objData.designation_of_interview_provider">
                      <td style="text-align:right!important;">সাক্ষাৎকার প্রদানকারীর পদবী </td>
                      <td style="text-align:left!important;"> : {{ objData.interview_provider_id }}</td>
                    </tr>
                    
                    <tr v-if="objData.mobile_of_interview_provider">
                      <td style="text-align:right!important;">সাক্ষাৎকার প্রদানকারীর মোবাইল</td>
                      <td style="text-align:left!important;"> : {{ objData.mobile_of_interview_provider }}</td>
                    </tr>
                    
                    <tr v-if="objData.interview_provider_date">
                      <td style="text-align:right!important;">তারিখ</td>
                      <td style="text-align:left!important;"> : {{ objData.interview_provider_date }}</td>
                    </tr>
                  </table>
                </td>


                <td style="padding: 0!important;">
                  <br>
                  <table class="signature-table">
                 
                    <tr v-if="objData.interview_receipient_name">
                      <td style="width:50%;text-align:right!important;">সাক্ষাৎকার গ্রহণকারীর নাম</td>
                      <td style="text-align:left!important;"> : {{ objData.interview_receipient_name }}</td>
                    </tr>
                    
                    <tr v-if="objData.interview_receipient_id">
                      <td style="text-align:right!important;">সাক্ষাৎকার গ্রহণকারীর আইডি</td>
                      <td style="text-align:left!important;"> : {{ objData.interview_receipient_id }}</td>
                    </tr>
                    
                    <tr v-if="objData.interview_receipient_mobile">
                      <td style="text-align:right!important;">সাক্ষাৎকার গ্রহণকারীর মোবাইল</td>
                      <td style="text-align:left!important;"> : {{ objData.interview_receipient_mobile }}</td>
                    </tr>
                    
                    <tr v-if="objData.interview_receipient_date">
                      <td style="text-align:right!important;">তারিখ</td>
                      <td style="text-align:left!important;"> : {{ objData.interview_receipient_date }}</td>
                    </tr>
                    
                  </table>
                </td>
                  </tr>
                </tbody>
               </table>
            </div>
           </div>
        </BCardText>
      </BCard>
    </div>
  </div>
</template>
<style scoped>
.table > :not(caption) > * > *{
    padding: inherit;;
  }
td{
  padding: px 10px;
  vertical-align: middle;
  font-size: 13px;
  padding-left: 5px !important;
  padding-right: 5px !important;
}
</style>