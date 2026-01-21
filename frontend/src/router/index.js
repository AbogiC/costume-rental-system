import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import CostumeList from '../views/CostumeList.vue'
import CostumeForm from '../views/CostumeForm.vue'
import RentCostume from '../views/RentCostume.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import AdminRentalsView from '../views/AdminRentalsView.vue'
import { useAuthStore } from '../stores/authStore'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { title: 'Login', requiresGuest: true },
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { title: 'Register', requiresGuest: true },
  },
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Dashboard', requiresAuth: true },
  },
  {
    path: '/costumes',
    name: 'costumes',
    component: CostumeList,
    meta: { title: 'Costume Catalogue', requiresAuth: true },
  },
  {
    path: '/costumes/new',
    name: 'new-costume',
    component: CostumeForm,
    meta: { title: 'Add Costume', requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/costumes/edit/:id',
    name: 'edit-costume',
    component: CostumeForm,
    meta: { title: 'Edit Costume', requiresAuth: true, requiresAdmin: true },
  },
  {
    path: '/rent',
    name: 'rent',
    component: RentCostume,
    meta: { title: 'Rent Costume', requiresAuth: true },
  },
  {
    path: '/admin/rentals',
    name: 'admin-rentals',
    component: AdminRentalsView,
    meta: { title: 'All Rentals', requiresAuth: true, requiresAdmin: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? `${to.meta.title} - Costume Rental` : 'Costume Rental'

  const authStore = useAuthStore()
  authStore.checkAuth()

  const isAuthenticated = authStore.isAuthenticated
  const isAdmin = authStore.isAdmin

  if (to.meta.requiresGuest && isAuthenticated) {
    return next('/')
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/login')
  }

  if (to.meta.requiresAdmin && !isAdmin) {
    return next('/')
  }

  next()
})

export default router
