import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import CostumeList from '../views/CostumeList.vue'
import CostumeForm from '../views/CostumeForm.vue'
import RentCostume from '../views/RentCostume.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Dashboard' },
  },
  {
    path: '/costumes',
    name: 'costumes',
    component: CostumeList,
    meta: { title: 'Costume Catalogue' },
  },
  {
    path: '/costumes/new',
    name: 'new-costume',
    component: CostumeForm,
    meta: { title: 'Add Costume' },
  },
  {
    path: '/costumes/edit/:id',
    name: 'edit-costume',
    component: CostumeForm,
    meta: { title: 'Edit Costume' },
  },
  {
    path: '/rent',
    name: 'rent',
    component: RentCostume,
    meta: { title: 'Rent Costume' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - Costume Rental` : 'Costume Rental'
  next()
})

export default router
