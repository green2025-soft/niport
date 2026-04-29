<script setup>
   const items = [
  { text: "Specialized Hospital", link: "niport/categories/specialized/faculties", icon: "fas fa-hospital" },
  { text: "Specialized Hospital List", link:"niport/survey/specialized", icon: "fas fa-list" },

  { text: "Medical Coll. & Univ.", link: "niport/categories/medical_college_university/faculties", icon: "fas fa-hospital-alt" },
  { text: "Medical Coll. & Univ. List", link: "niport/survey/medical_college_university", icon: "fas fa-list" },

  { text: "Dist. Level Health Facility", link: "niport/categories/district_level/faculties", icon: "fas fa-clinic-medical" },
  { text: "Dist. Health Facility List", link: "niport/survey/district_level", icon: "fas fa-list" },

  { text: "Upazila Health Facility", link: "niport/categories/upazila_level/faculties", icon: "fas fa-hospital-user" },
  { text: "Upazila Health Facility List", link: "niport/survey/upazila_level", icon: "fas fa-list" },

  { text: "Union Health Facility", link: "niport/categories/union_level/faculties", icon: "fas fa-hospital-symbol" },
  { text: "Union Health Facility List", link: "niport/survey/upazila_level", icon: "fas fa-list" },

  { text: "Comm. Health Facility", link: "niport/categories/community_clinic/faculties", icon: "fas fa-plus-square" },
  { text: "Comm. Health Facility List", link: "niport/survey/community_clinic", icon: "fas fa-list" }
]
 
</script>
<template>
  <div class="container-fluid py-3">
    <div class="row g-3">
      <div
        class="col-md-6"
        v-for="(item, index) in items"
        :key="index"
      >
        <!-- পুরো card clickable -->
        <RouterLink
          :to="`/${item.link}`"
          class="text-decoration-none"
        >
          <div class="card shadow-sm border-0 h-100 hover-card">
            <div class="card-body d-flex align-items-center">
              
              <!-- Icon -->
              <div class="icon-box me-3">
                <i :class="item.icon"></i>
              </div>

              <!-- Text -->
              <div>
                <h6 class="mb-0 text-dark">{{ item.text }}</h6>
              </div>

            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  
      
        <div class="card mt-4">
          <div class="card-body">
             <div class="col-12 col-sm-12 col-md-12 col-xs-12" data-url="">
              <div class="info-box">
                  <div class="info-box-content">
                      <h4 class="info-box-text">Note: 
                          <ul>
                          <li><span> College = Coll. </span></li>
                          <li><span> University = Univ. </span></li>
                          <li><span> District = Dist. </span></li>
                          <li><span> Community = Comm. </span></li>
                          </ul>
                      </h4>
                  </div>
              </div>
          </div>
          </div>
        </div>
    </div>
</template>
 <style scoped>
  .icon-box {
  width: 55px;
  height: 55px;
  background: #17a2b8;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
  font-size: 22px;
}

/* hover effect */
.hover-card {
  transition: all 0.2s ease;
  cursor: pointer;
}
.hover-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
  </style>
