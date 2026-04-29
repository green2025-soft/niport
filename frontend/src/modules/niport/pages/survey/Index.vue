<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useResourceApiClient } from '@/composables/resourceApiClient'
import { useForm } from '@/utilities/methods'
import { useRoute } from "vue-router";
import * as XLSX from "xlsx"
import vSelect from 'vue-select'
const route = useRoute();


const type = computed(() => route.params.type);

//  Setup
const title = ref('Survey List')
const bUrl = `niport/surveys`;
const tablebUrl = `niport/surveys?type=${type.value}`;
const category = ref('');
const faculties = ref([]);
const outdoorDepartments = ref([]);
const indoorDepartments = ref([]);
const users = ref([]);
onMounted(async() => {
  try{
    const dependencies            = await customGet('/niport/list-dependencies', {'type':type.value});
    title.value = dependencies?.category?.category_name
    category.value = dependencies?.category
    faculties.value = dependencies?.faculties
    outdoorDepartments.value = Object.values(dependencies?.outdoorDepartments || {})
    indoorDepartments.value = dependencies?.indoorDepartments
    users.value = dependencies?.users
    await loadDistricts(form.value.division_id);
    
  }catch (error) {
    router.replace('/niport/404')
  }

})

const {
  askDelete,
  confirmDelete,
  customGet

} = useResourceApiClient(bUrl, title.value, true)
const dataTableRef = ref(null)


const exportFullHtml = () => {
    const element = document.querySelector("#dataTablePrint")

  
  const workbook = XLSX.utils.table_to_book(element, {
    sheet: "Sheet1"
  })

  XLSX.writeFile(workbook, "data.xlsx")
}

const { form, reset } = useForm({
  division_id: '',
  district_id: '',
  upazila_id: '',
  union_id: '',
  faculty_id: '',
  out_department_id: '',
  in_department_id: '',
  created_by: '',
})


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
  form.value.district_id = ''
})

watch(() => form.value.district_id, (val) => {
  loadUpazilas(val)
  form.value.upazila_id = ''
})

watch(() => form.value.upazila_id, (val) => {
  loadUnions(val)
  form.value.union_id = ''
})

const isLoading = ref(false);
async function formSearch() {
  isLoading.value = true; 
  await dataTableRef.value.refresh();
  isLoading.value = false;

}
</script>

<template>
  <ConfirmDelete ref="confirmDeleteModal"  @confirm="() => confirmDelete(() => dataTableRef.refresh())" />
     <div class="container-fluid">
      <div class="card">
        <div class="card-body">
          
          <div class="row">
            <BaseFormGroup class="col-md-3" label="বিভাগ" labelCols="12">
                <ResourceSelect
                  v-model="form.division_id"
                  bUrl="niport/division"
                  placeholder="--সিলেক্ট--"
                  :clearable="false"
                  :labelField="(item) => `${item.bn_name}`"
                />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="জেলা" labelCols="12">
                <vSelect
                      v-model="form.district_id"
                      :options="districts"
                      :reduce="district => district.id"
                      label="bn_name"
                      :disabled="!form.division_id"
                      placeholder="--সিলেক্ট--"

                    />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="উপজেলা" labelCols="12">
                <vSelect
                      v-model="form.upazila_id"
                      :options="upazilas"
                      label="bn_name"
                      :reduce="item => item.id"
                      :disabled="!form.district_id"
                      placeholder="--সিলেক্ট--"
                    />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="ইউনিয়ন" labelCols="12">
                <vSelect
                        v-model="form.union_id"
                        :options="unions"
                        label="bn_name"
                        :reduce="item => item.id"
                        :disabled="!form.upazila_id"
                        placeholder="--সিলেক্ট--"
                      />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="ফেসিলিটি" labelCols="12">
              <vSelect
                        v-model="form.faculty_id"
                        :options="faculties"
                        label="faculty_name"
                        :reduce="item => item.id"
                        placeholder="--সিলেক্ট--"
                      />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="আউটডোর বিভাগ" labelCols="12">
              <vSelect
                        v-model="form.out_department_id"
                        :options="outdoorDepartments"
                        label="department_name"
                        :reduce="item => item.id"
                        placeholder="--সিলেক্ট--"
                      />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="ইনডোর বিভাগ" labelCols="12">
              <vSelect
                        v-model="form.in_department_id"
                        :options="indoorDepartments"
                        label="department_name"
                        :reduce="item => item.id"
                        placeholder="--সিলেক্ট--"
                      />
            </BaseFormGroup>
            <BaseFormGroup class="col-md-3" label="User" labelCols="12">
              <vSelect
                        v-model="form.created_by"
                        :options="users"
                        label="name"
                        :reduce="item => item.id"
                        placeholder="--সিলেক্ট--"
                      />
            </BaseFormGroup>
          </div>
        </div>
        <div class="card-footer text-center">
          <LoadingButton :loading="isLoading" class="d-inline-block" size="sm" variant="primary" @click="formSearch" >
            
           <i class="fa fa-search"></i>Search
          </LoadingButton>
        </div>
      </div>
      <br>
       <div class="card card-outline card-info">
        <div class="card-header">
             <h2 class="card-title">
              <!-- <i class="fas fa-tasks"></i> {{ title }} -->
            </h2>
            <div class="card-tools">
              <BButton  variant="primary" title="Export to Excel" size="sm" @click="exportFullHtml()">
                <i class="fas fa-download"></i> Export
              </BButton> &nbsp;
              <RouterLink class="btn btn-info btn-sm" :to="`/niport/categories/${type}/faculties`"><i class="fas fa-plus"></i> Add New Survey</RouterLink>
            </div>
        </div>
        <div class="card-body">

          
          <DataTable
           ref="dataTableRef"
                    :fields="[
                      { key: 'sl', label: 'SL' },
                      { key: 'questionnaire_id', label: 'Questionnaire ID', align: 'center'  },
                      { key: 'faculty.faculty_name', label: 'ফেসিলিটি' },
                      { key: 'organization_location', label: 'প্রতিষ্ঠানের অবস্থান', isChange: true },
                      { key: 'latitude', label: 'অক্ষাংশ দ্রাঘিমাংশ',  isChange: true },
                      { key: 'neighborhood', label: 'মহল্লা/ওয়ার্ড নম্বর/রাস্তার নাম', isChange: true  },
                      { key: 'company_full_name', label: 'স্বাস্থ্যসেবা প্রতিষ্ঠানের পূর্ণনাম'  },
                      { key: 'designation_of_head_of_company', label: 'প্রতিষ্ঠান প্রধানের পদবী' },
                      { key: 'name_of_head_of_company', label: 'প্রতিষ্ঠান প্রধানের নাম' },                    
                      { key: 'created_by.name', label: 'User' },                    
                      { key: 'actions', label: 'অ্যাকশন' }
                    ]"
                    :bUrl="tablebUrl"
                    :extraParams="form"
                     
                  >

                <template #cell-organization_location="{ item }">
                    {{ item.division?.bn_name?`বিভাগ :${item.division?.bn_name}`:'' }}
                    <template v-if="item.district?.bn_name">
                        <br>জেলা : {{ item.district?.bn_name }}
                    </template>
                    <template v-if="item.upazila?.bn_name">
                        <br>উপজেলা : {{ item.upazila?.bn_name }}
                    </template>
                    <template v-if="item.union?.bn_name">
                        <br>ইউনিয়ন : {{ item.union?.bn_name }}
                    </template>
                </template>
                <template #cell-latitude="{ item }">
                    অক্ষাংশ : {{ item.latitude }}
                    <br>দ্রাঘিমাংশ : {{ item.longitude }}
                </template>
                <template #cell-neighborhood="{ item }">
                    মহল্লার নাম : {{ item.moholla_name }}
                    <br>ওয়ার্ড নম্বর : {{ item.word_no }}
                    <br>রাস্তার নাম : {{ item.road_no }}
                    <br>হোল্ডিং নম্বর : {{ item.holding_no }}
                </template>
            
                
               
                <template #actions="{ rowItem }">
                    <div class="btn-group dropleft">                      
                      <RouterLink class="btn btn-outline-primary btn-sm"  :to="`/niport/survey/${rowItem.id}/view`">
                        <i class="fa fa-table"></i>
                      </RouterLink>
                        <BButton variant="primary" class="dropdown-toggle dropdown_toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        </BButton>

                      <ul class="dropdown-menu" >
                          <li>
                            <RouterLink class="dropdown-item" :to="`/niport/survey/${rowItem.id}/edit`"><i class="fas fa-edit"></i> Edit</RouterLink>
                          
                        </li>
                              <li> <div class="dropdown-divider"></div></li>
                          <li><a class="dropdown-item" href=""  @click.prevent="askDelete(rowItem.id)"><i class="fa fa-trash"></i> Delete</a></li>
                      </ul>
                  
                  </div>
                </template>
            </DataTable>
          </div>
      </div>
        
  </div>
</template>
