<script setup>
import { ref, watch, computed, onMounted   } from 'vue'
import { useRoute } from "vue-router";
import { useResourceApiClient } from '@/composables/resourceApiClient'
import { useSettingsStore } from '@/store/settings-store'
import { useImageUrl  } from '@/utilities/methods'
const imageUrl = ref('')
const govtLogoUrl = ref('')
const settingsStore = useSettingsStore()

const route = useRoute();
const type = computed(() => route.params.type);

const category = ref(null);
const faculties = ref([]);

//  Setup
const title = 'Category Faculties'
const bUrl =`niport/categories/${type.value}/faculties`;

const {
getList
} = useResourceApiClient(bUrl, title)


const fetchFaculties = async () => {
  try {
    const res = await getList();

    category.value = res;
    faculties.value = res.faculties;
  } catch (error) {
    console.error(error);
  }
};

onMounted(() => {
 settingsStore.fetchSettings().then(() => {
    imageUrl.value = useImageUrl(settingsStore.data.app_logo)
    govtLogoUrl.value = useImageUrl(settingsStore.data.icon_logo)
  })

  fetchFaculties()
  
})






</script>

<template>
    <!-- Delete Confirmation -->
 


  <div class="px-3">
    <div class="mx-auto w-100" >
      <BCard 
       header-tag="header"
      header-bg-variant="info"
      header-text-variant="white"
      >
      <template #header>
        <h2 class="card-title fw-bold text-white " >
        </h2>
         <div class="card-tools">
              <RouterLink class="btn btn-primary btn-sm" :to="`/niport/survey/${type}`"><i class="fas fa-list"></i> Survey List</RouterLink>
            </div>
      </template>
         <BCardText >

            <div class="page" v-if="category">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start">
              
              <!-- Empty left -->
              <div></div>

              <!-- Logo -->
              <div class="text-center">
                <img v-if="govtLogoUrl":src="govtLogoUrl" class="logo">
              </div>

              <!-- SL -->
              <div class="text-end fw-bold">
                <div >SL. :
                  <span class="sl-box">
                    <span></span><span></span><span></span><span></span><span></span>
                  </span>
                </div>
                <div class="mt-2">Checklist No. {{ category.checklist_no }}</div>
              </div>
            </div>

            <!-- Title -->
            <h5 class="title mt-4 fw-bold">
              Study Title : {{ category.study_title }}
            </h5>

            <!-- Subtitle -->
            <h5 class="title mt-3 fw-bold">
              Instrument Category: {{ category.category_name }}
            </h5>

            <!-- Table -->
            <div class="mt-3 table-responsive">
              <table class="table text-center fw-bold">
                <thead>
                  <tr >
                    <th>Name of Health Facility</th>
                    <th style="width:120px;">Code</th>
                  </tr>
                </thead>
                <tbody class="text-start">
                  <tr v-for="faculty in faculties" :key="faculty.id">
                    <td>
                      
                      <router-link :to="`/niport/survey/create/${faculty.category_id}/${faculty.id}`" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover ">  {{ faculty.faculty_name }}</router-link>
                    </td>
                    <td class="text-center">{{ faculty.code_no }}</td>
                  </tr>
                 
                </tbody>
              </table>
            </div>

            <!-- Respondent -->
            <h5 class="text-center mt-4 fw-bold">
              Respondent : {{ category.respondent }}
            </h5>

            <!-- Logo Text -->
            <div class="text-center ">
               <img v-if="imageUrl":src="imageUrl" style=" width: 85px;">
              
            </div>

            <!-- Address -->
            <div class="text-center small-text">
              <p>
                
                <div v-html="settingsStore.data.app_address"></div>
              </p>

              <p class="mt-3">
                 ‍<span class="fw-bold">Conducted By</span> <br>
                <div v-html="settingsStore.data.conducted_by"></div>
                
              </p>
            </div>

          </div>
          
        </BCardText>

      </BCard>
    </div>
  </div>



</template>
<style scoped>
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
      padding: 40px;
      /* max-width: 1000px; */
      margin: auto;
      border: 1px solid #ddd;
    }
    .title {
      font-weight: 600;
      text-align: center;
    }
    .table th, .table td {
      border: 1px solid #000 !important;
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
</style>