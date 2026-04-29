<script setup>
import { ref, watch, computed   } from 'vue'
import { useResourceApiClient } from '@/composables/resourceApiClient'
import { useForm } from '@/utilities/methods'

//  Setup
const title = 'Upazila'
const bUrl = 'niport/upazila'

const {
  update,
  create,
  askDelete,
  confirmDelete,
  formErrors,
  isSubmitting,
} = useResourceApiClient(bUrl, title)

//  Form Setup
const { form, reset } = useForm({
  division_id: '',
  district_id: '',
  name: '',
  bn_name: '',
})
const errors = ref([])
const showModal = ref(false)
const dataTableRef = ref(null)

const isEdit = ref(false)
//  Modal Open/Edit

function openModal(item = null) {
  errors.value = []
  reset()
  
  if (item){ 
    Object.assign(form.value, item)
    isEdit.value = true
   form.value.division_id = item.division.id
  
    
  }else{
    isEdit.value = false 
  }
  showModal.value = true
}

//  Save/Create/Update Item
async function saveItem() {
  try {
    if (form.value.id) {
      await update(form.value.id, form.value)
    } else {
      await create(form.value)
    }

    await dataTableRef.value?.refresh()
    reset()
    showModal.value = false
  } catch (error) {
    errors.value = formErrors.value
  }
}




watch(() => form.value.division_id, (newVal, oldVal) => {
  if (oldVal){
      form.value.district_id = ''
  }
})


</script>

<template>
    <!-- Delete Confirmation -->
  <ConfirmDelete
    ref="confirmDeleteModal"
    @confirm="() => confirmDelete(() => dataTableRef.refresh())"
  />


  <div class="px-3">
    <div class="mx-auto w-100" >
      <div class="card card-outline card-info">
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center">
          <h2 class="card-title m-0">
            <i class="fas fa-tasks"></i> {{ title }}
          </h2>
          <BButton variant="primary" size="sm" @click="openModal()">
            <i class="fas fa-plus"></i> Add New
          </BButton>
        </div>

        <!-- Data Table -->
        <div class="card-body">
          <DataTable
            ref="dataTableRef"
            :fields="[
              { key: 'sl', label: 'SL' },
              { key: 'division.bn_name', label: 'বিভাগের নাম' },
              { key: 'district.bn_name', label: 'জেলার নাম' },
              { key: 'name', label: 'উপজেলার নাম ইংলিশ' },
              { key: 'bn_name', label: 'উপজেলার নাম বাংলা' },
              { key: 'actions', label: 'Actions' }
            ]"
            :bUrl="bUrl"
            :isBranch="true"
          >
            <!-- Actions -->
            <template #actions="{ rowItem }">
              
              <div class="btn-group dropleft">
                <BButton variant="outline-primary" @click="openModal(rowItem)">
                  <i class="fa fa-edit"></i>
                </BButton>
                <BButton variant="outline-danger" @click="askDelete(rowItem.id)">
                  <i class="fa fa-trash"></i>
                </BButton>
              </div>
            </template>
          </DataTable>
        </div>
      </div>
    </div>
  </div>


    <!-- Form Modal -->
  <FormModal
    v-model="showModal"
    :title="form.id ? `Edit ${title}` : `Add ${title}`"
    :loading="isSubmitting"
    
    @submit="saveItem"
  >
    <ValidationErrors :errors="errors" />

    <BaseFormGroup label="বিভাগের নাম" labelCols="12" required>
       <ResourceSelect
              v-model="form.division_id"
              bUrl="niport/division"
              :clearable="false"
              :labelField="(item) => `${item.bn_name}`"
              :isEdit="isEdit"
            />
    </BaseFormGroup>
    <BaseFormGroup label="জেলার নাম" labelCols="12" required>
       <ResourceSelect
              v-model="form.district_id"
              bUrl="niport/district"
              :clearable="false"
              :labelField="(item) => `${item.bn_name}`"
              :isEdit="isEdit"
             :extraParams="form.division_id ? { division_id: form.division_id } : {}"
              
            />
    </BaseFormGroup>
    
    <BaseFormGroup label="উপজেলার নাম বাংলা" labelCols="12" required>
      <BFormInput v-model="form.name" />
    </BaseFormGroup>
    <BaseFormGroup label="উপজেলার নাম ইংলিশ" labelCols="12" required>
      <BFormInput v-model="form.bn_name" />
    </BaseFormGroup>


  </FormModal>

</template>