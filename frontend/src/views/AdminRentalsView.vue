<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">All Rentals</h1>

    <div v-if="loading" class="text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
    </div>

    <div v-else-if="rentals.length === 0" class="text-center text-gray-500">No rentals found.</div>

    <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="rental in rentals" :key="rental.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center">
                <div class="flex-1">
                  <h3 class="text-lg font-medium text-gray-900">{{ rental.costume_name }}</h3>
                  <p class="text-sm text-gray-500">
                    Rented by: {{ rental.user_name }} ({{ rental.user_email }})
                  </p>
                  <p class="text-sm text-gray-500">
                    Dates: {{ formatDate(rental.rental_date) }} to
                    {{ formatDate(rental.return_date) }}
                  </p>
                  <p class="text-sm text-gray-500">Total: ${{ rental.total_price }}</p>
                </div>
                <div class="ml-4">
                  <span
                    :class="getStatusClass(rental.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ rental.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { rentalService } from '@/services/api'

const rentals = ref([])
const loading = ref(true)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString()
}

const getStatusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'active':
      return 'bg-green-100 text-green-800'
    case 'completed':
      return 'bg-blue-100 text-blue-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const fetchRentals = async () => {
  try {
    rentals.value = await rentalService.getAllRentals()
  } catch (error) {
    console.error('Failed to fetch rentals:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchRentals()
})
</script>
