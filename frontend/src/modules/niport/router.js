import Index from "./pages/Index.vue";
import Page404 from "@/components/Page404.vue"; // You can replace if module specific 404

 const lazy = (view) => () => import(`./pages/${view}.vue`);

const defaultAuth = true;
const defaultMeta = { requiresAuth: defaultAuth };

const makeRoute = (path, component, title) => ({
  path,
  component,
  meta: { title, ...defaultMeta },
});

const routes = {
  path: "/niport",
  component: Index,
  redirect: "/niport/dashboard",
  children: [
   makeRoute("division", lazy("Division"), "Division"),
   makeRoute("district", lazy("District"), "District"),
   makeRoute("upazila", lazy("Upazila"), "Upazila"),
   makeRoute("union", lazy("Union"), "Union"),
   makeRoute("categories/:type/faculties", lazy("CategoryFaculties"), "Category Faculties"),
  //  makeRoute("survey/:category_id/:faculty_id", lazy("SurveyForm"), "Survey Form"),

  {
    path: "survey/:type",
    component: () => import('@/modules/niport/pages/survey/Index.vue'),
    meta: { title: "Survey List", ...defaultMeta},
  },
   {
    path: "survey/create/:category_id/:faculty_id",
    component: () => import('@/modules/niport/pages/survey/Form.vue'),
    meta: { title: "Survey Form", ...defaultMeta},
  },
  {
    path: "survey/:view_id/view",
    component: () => import('@/modules/niport/pages/survey/View.vue'),
    meta: { title: "Survey Information", ...defaultMeta},
  },
  {
    path: "survey/:edit_id/edit",
    component: () => import('@/modules/niport/pages/survey/Form.vue'),
    meta: { title: "Edit Survey", ...defaultMeta},
  },
    {
      path: ":catchAll(.*)",
      component: Page404
    }
  ]
};

export default routes;
