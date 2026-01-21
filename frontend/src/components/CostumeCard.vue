<template>
  <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition duration-300">
    <img
      :src="costume.image_url || 'https://via.placeholder.com/300x200?text=Costume'"
      :alt="costume.name"
      class="w-full h-48 object-cover"
    />

    <div class="p-4">
      <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-lg">{{ costume.name }}</h3>
        <span
          :class="[
            'px-2 py-1 text-xs rounded-full',
            costume.quantity_available > 0
              ? 'bg-green-100 text-green-800'
              : 'bg-red-100 text-red-800',
          ]"
        >
          {{ costume.quantity_available > 0 ? 'Available' : 'Out of Stock' }}
        </span>
      </div>

      <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ costume.description }}</p>

      <div class="flex items-center justify-between mb-4">
        <span class="text-sm text-gray-500">{{ costume.category }} • Size: {{ costume.size }}</span>
        <span class="font-bold text-blue-600">${{ costume.price_per_day }}/day</span>
      </div>

      <div class="flex justify-between space-x-2">
        <router-link
          v-if="authStore.isAdmin"
          :to="`/costumes/edit/${costume.id}`"
          class="bg-blue-100 text-blue-600 text-center py-2 px-4 rounded hover:bg-blue-200 transition duration-300"
        >
          Edit
        </router-link>
        <button
          v-if="authStore.isAdmin"
          @click="$emit('delete', costume.id)"
          class="bg-red-100 text-red-600 py-2 px-4 rounded hover:bg-red-200 transition duration-300"
        >
          Delete
        </button>
        <router-link
          :to="{ name: 'rent', query: { costumeId: costume.id } }"
          :disabled="costume.quantity_available === 0"
          :class="[
            'text-center py-2 px-4 rounded transition duration-300',
            costume.quantity_available > 0
              ? 'bg-green-100 text-green-600 hover:bg-green-200'
              : 'bg-gray-100 text-gray-400 cursor-not-allowed',
            authStore.isAdmin ? 'flex-1' : 'flex-1',
          ]"
        >
          Rent
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

defineProps({
  costume: {
    type: Object,
    required: true,
  },
})

defineEmits(['delete'])
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
