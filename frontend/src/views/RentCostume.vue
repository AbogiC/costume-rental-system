<template>
  <div class="rent-costume">
    <h1 class="text-3xl font-bold mb-6">Rent a Costume</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2">
        <div v-if="costumeStore.loading" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="costume in costumeStore.availableCostumes"
            :key="costume.id"
            :class="[
              'bg-white rounded-lg shadow overflow-hidden cursor-pointer transition duration-300',
              selectedCostume?.id === costume.id ? 'ring-2 ring-blue-500' : 'hover:shadow-lg',
            ]"
            @click="selectCostume(costume)"
          >
            <img
              :src="costume.image_url || 'https://via.placeholder.com/300x200?text=Costume'"
              :alt="costume.name"
              class="w-full h-48 object-cover"
            />
            <div class="p-4">
              <h3 class="font-bold text-lg mb-2">{{ costume.name }}</h3>
              <p class="text-gray-600 text-sm mb-3">
                {{ costume.category }} • Size: {{ costume.size }}
              </p>
              <div class="flex justify-between items-center">
                <span class="font-bold text-blue-600">${{ costume.price_per_day }}/day</span>
                <span class="text-sm text-green-600"
                  >{{ costume.quantity_available }} available</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <div v-if="selectedCostume" class="bg-white rounded-lg shadow p-6 sticky top-6">
          <h2 class="text-xl font-bold mb-4">Rental Details</h2>

          <div class="mb-6">
            <h3 class="font-semibold text-lg mb-2">{{ selectedCostume.name }}</h3>
            <p class="text-gray-600 mb-4">{{ selectedCostume.description }}</p>
            <div class="flex justify-between mb-2">
              <span>Category:</span>
              <span class="font-semibold">{{ selectedCostume.category }}</span>
            </div>
            <div class="flex justify-between mb-2">
              <span>Size:</span>
              <span class="font-semibold">{{ selectedCostume.size }}</span>
            </div>
            <div class="flex justify-between mb-4">
              <span>Price per day:</span>
              <span class="font-bold text-blue-600">${{ selectedCostume.price_per_day }}</span>
            </div>
          </div>

          <form @submit.prevent="submitRental" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
              <input
                type="text"
                v-model="rentalForm.renter_name"
                required
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
              <input
                type="email"
                v-model="rentalForm.renter_email"
                required
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
              <input
                type="tel"
                v-model="rentalForm.renter_phone"
                class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rental Date *</label>
                <input
                  type="date"
                  v-model="rentalForm.rental_date"
                  :min="today"
                  required
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Return Date *</label>
                <input
                  type="date"
                  v-model="rentalForm.return_date"
                  :min="minReturnDate"
                  required
                  class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
              </div>
            </div>

            <div v-if="rentalDuration > 0" class="bg-gray-50 p-4 rounded-lg">
              <div class="flex justify-between mb-2">
                <span>Duration:</span>
                <span>{{ rentalDuration }} day{{ rentalDuration > 1 ? 's' : '' }}</span>
              </div>
              <div class="flex justify-between text-lg font-bold">
                <span>Total Price:</span>
                <span class="text-green-600">${{ totalPrice.toFixed(2) }}</span>
              </div>
            </div>

            <button
              type="submit"
              :disabled="submitting"
              class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ submitting ? 'Processing...' : 'Confirm Rental' }}
            </button>
          </form>
        </div>

        <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
          <div class="text-yellow-800">
            <h3 class="font-bold text-lg mb-2">Select a Costume</h3>
            <p>Please select a costume from the left to start the rental process.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCostumeStore } from '@/stores/costumeStore'
import { rentalService } from '@/services/api'

const costumeStore = useCostumeStore()
const selectedCostume = ref(null)
const submitting = ref(false)

const rentalForm = ref({
  renter_name: '',
  renter_email: '',
  renter_phone: '',
  rental_date: '',
  return_date: '',
})

const today = new Date().toISOString().split('T')[0]

const minReturnDate = computed(() => {
  return rentalForm.value.rental_date || today
})

const rentalDuration = computed(() => {
  if (!rentalForm.value.rental_date || !rentalForm.value.return_date) return 0
  const start = new Date(rentalForm.value.rental_date)
  const end = new Date(rentalForm.value.return_date)
  const diffTime = Math.abs(end - start)
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1
})

const totalPrice = computed(() => {
  if (!selectedCostume.value || rentalDuration.value === 0) return 0
  return selectedCostume.value.price_per_day * rentalDuration.value
})

onMounted(async () => {
  await costumeStore.fetchCostumes()
})

const selectCostume = (costume) => {
  selectedCostume.value = costume
}

const submitRental = async () => {
  if (!selectedCostume.value) {
    alert('Please select a costume first')
    return
  }

  submitting.value = true
  try {
    const rentalData = {
      costume_id: selectedCostume.value.id,
      ...rentalForm.value,
      total_price: totalPrice.value,
    }

    await rentalService.createRental(rentalData)

    alert('Rental successful!')

    // Reset form
    rentalForm.value = {
      renter_name: '',
      renter_email: '',
      renter_phone: '',
      rental_date: '',
      return_date: '',
    }
    selectedCostume.value = null

    // Refresh costume list to update availability
    await costumeStore.fetchCostumes()
  } catch (error) {
    alert('Failed to create rental: ' + error.message)
  } finally {
    submitting.value = false
  }
}
</script>
