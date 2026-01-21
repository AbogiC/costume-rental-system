<template>
  <div class="home">
    <h1 class="text-3xl font-bold mb-6">Costume Rental Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Costumes</h3>
        <p class="text-3xl font-bold text-blue-600">{{ stats.totalCostumes }}</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Available Costumes</h3>
        <p class="text-3xl font-bold text-green-600">{{ stats.availableCostumes }}</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Total Rentals</h3>
        <p class="text-3xl font-bold text-purple-600">{{ stats.totalRentals }}</p>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Active Rentals</h3>
        <p class="text-3xl font-bold text-yellow-600">{{ stats.activeRentals }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">Recent Costumes</h2>
        </div>
        <div class="p-6">
          <div v-if="loading" class="text-center py-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
          </div>
          <div v-else>
            <div
              v-for="costume in recentCostumes"
              :key="costume.id"
              class="flex items-center justify-between py-3 border-b last:border-b-0"
            >
              <div>
                <h4 class="font-semibold">{{ costume.name }}</h4>
                <p class="text-sm text-gray-600">
                  {{ costume.category }} • Size: {{ costume.size }}
                </p>
              </div>
              <div class="text-right">
                <span class="font-bold">${{ costume.price_per_day }}/day</span>
                <div
                  :class="[
                    'text-sm',
                    costume.quantity_available > 0 ? 'text-green-600' : 'text-red-600',
                  ]"
                >
                  {{ costume.quantity_available }} available
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
          <h2 class="text-xl font-bold">Quick Actions</h2>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <router-link
              to="/costumes/new"
              class="block w-full bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition duration-300"
            >
              Add New Costume
            </router-link>
            <router-link
              to="/costumes"
              class="block w-full bg-green-600 text-white text-center py-3 rounded-lg hover:bg-green-700 transition duration-300"
            >
              View All Costumes
            </router-link>
            <router-link
              to="/rent"
              class="block w-full bg-purple-600 text-white text-center py-3 rounded-lg hover:bg-purple-700 transition duration-300"
            >
              Rent a Costume
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCostumeStore } from '@/stores/costumeStore'

const costumeStore = useCostumeStore()
const loading = ref(false)
const stats = ref({
  totalCostumes: 0,
  availableCostumes: 0,
  totalRentals: 0,
  activeRentals: 0,
})

const recentCostumes = ref([])

onMounted(async () => {
  loading.value = true
  await costumeStore.fetchCostumes()

  stats.value.totalCostumes = costumeStore.costumes.length
  stats.value.availableCostumes = costumeStore.costumes.filter(
    (c) => c.quantity_available > 0,
  ).length

  // Get 5 most recent costumes
  recentCostumes.value = [...costumeStore.costumes]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5)

  loading.value = false
})
</script>
