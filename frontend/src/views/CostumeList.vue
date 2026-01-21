<template>
  <div class="costume-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold">Costume Catalogue</h1>
      <router-link
        to="/costumes/new"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300"
      >
        Add New Costume
      </router-link>
    </div>

    <div class="mb-6">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search costumes..."
        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
      />
    </div>

    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
      <p class="mt-4 text-gray-600">Loading costumes...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      {{ error }}
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <CostumeCard
        v-for="costume in filteredCostumes"
        :key="costume.id"
        :costume="costume"
        @delete="handleDelete"
      />
    </div>

    <div v-if="!loading && filteredCostumes.length === 0" class="text-center py-12">
      <p class="text-gray-600 text-lg">No costumes found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useCostumeStore } from '@/stores/costumeStore'
import CostumeCard from '@/components/CostumeCard.vue'

const costumeStore = useCostumeStore()
const searchQuery = ref('')

const loading = computed(() => costumeStore.loading)
const error = computed(() => costumeStore.error)

const filteredCostumes = computed(() => {
  const query = searchQuery.value.toLowerCase()
  return costumeStore.costumes.filter(
    (costume) =>
      costume.name.toLowerCase().includes(query) ||
      costume.description.toLowerCase().includes(query) ||
      costume.category.toLowerCase().includes(query),
  )
})

onMounted(async () => {
  await costumeStore.fetchCostumes()
})

const handleDelete = async (id) => {
  if (confirm('Are you sure you want to delete this costume?')) {
    try {
      await costumeStore.deleteCostume(id)
    } catch (err) {
      alert('Failed to delete costume: ' + err.message)
    }
  }
}
</script>
