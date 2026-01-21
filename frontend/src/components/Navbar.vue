<template>
  <nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center space-x-8">
          <router-link to="/" class="text-xl font-bold text-blue-600"> Costume Rental </router-link>

          <div class="hidden md:flex space-x-6">
            <router-link
              to="/"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition duration-300',
                route.name === 'home'
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <i class="fas fa-home mr-2"></i>Home
            </router-link>

            <router-link
              to="/costumes"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition duration-300',
                route.name && route.name.includes('costume')
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <i class="fas fa-tshirt mr-2"></i>Costumes
            </router-link>

            <router-link
              to="/rent"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition duration-300',
                route.name === 'rent'
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <i class="fas fa-shopping-cart mr-2"></i>Rent
            </router-link>

            <router-link
              v-if="authStore.isAdmin"
              to="/costumes/new"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition duration-300',
                route.name === 'new-costume'
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <i class="fas fa-plus mr-2"></i>Add Costume
            </router-link>

            <router-link
              v-if="authStore.isAdmin"
              to="/admin/rentals"
              :class="[
                'px-3 py-2 rounded-md text-sm font-medium transition duration-300',
                route.name === 'admin-rentals'
                  ? 'bg-blue-100 text-blue-600'
                  : 'text-gray-700 hover:bg-gray-100',
              ]"
            >
              <i class="fas fa-list mr-2"></i>All Rentals
            </router-link>
          </div>
        </div>

        <div class="flex items-center space-x-4" v-if="authStore.isAuthenticated">
          <div class="relative" v-if="authStore.isAdmin">
            <i class="fas fa-bell text-gray-600 hover:text-blue-600 cursor-pointer"></i>
            <span
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
            >
              3
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
              <i class="fas fa-user text-blue-600"></i>
            </div>
            <span class="hidden md:inline text-sm font-medium">{{ authStore.user.name }}</span>
            <button @click="logout" class="ml-4 text-gray-700 hover:text-red-600">
              <i class="fas fa-sign-out-alt"></i>
            </button>
          </div>
        </div>
        <div v-else class="flex items-center space-x-4">
          <router-link to="/login" class="text-gray-700 hover:text-blue-600">Login</router-link>
          <router-link
            to="/register"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700"
            >Register</router-link
          >
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const logout = () => {
  authStore.logout()
  router.push('/login')
}
</script>
