<template>
  <div class="costume-form max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">{{ isEditing ? 'Edit Costume' : 'Add New Costume' }}</h1>

    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
    </div>

    <div v-else>
      <form @submit.prevent="handleSubmit" class="bg-white rounded-lg shadow p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Costume Name *</label>
            <input
              type="text"
              v-model="form.name"
              required
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select
              v-model="form.category"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select Category</option>
              <option value="Historical">Historical</option>
              <option value="Fantasy">Fantasy</option>
              <option value="Horror">Horror</option>
              <option value="Comic">Comic</option>
              <option value="Movie">Movie Character</option>
              <option value="Animal">Animal</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
            <select
              v-model="form.size"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">Select Size</option>
              <option value="XS">XS</option>
              <option value="S">S</option>
              <option value="M">M</option>
              <option value="L">L</option>
              <option value="XL">XL</option>
              <option value="One Size">One Size</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Price per Day ($) *</label>
            <input
              type="number"
              v-model="form.price_per_day"
              step="0.01"
              min="0"
              required
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Available *</label>
            <input
              type="number"
              v-model="form.quantity_available"
              min="0"
              required
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
            <input
              type="url"
              v-model="form.image_url"
              placeholder="https://example.com/image.jpg"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
          <textarea
            v-model="form.description"
            rows="4"
            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-4 pt-6">
          <router-link
            to="/costumes"
            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-300"
          >
            Cancel
          </router-link>
          <button
            type="submit"
            :disabled="submitting"
            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Saving...' : isEditing ? 'Update' : 'Create' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useCostumeStore } from '@/stores/costumeStore'

const route = useRoute()
const router = useRouter()
const costumeStore = useCostumeStore()

const isEditing = computed(() => route.name === 'edit-costume')
const costumeId = computed(() => route.params.id)

const form = ref({
  name: '',
  description: '',
  category: '',
  size: '',
  price_per_day: 0,
  quantity_available: 1,
  image_url: '',
})

const loading = ref(false)
const submitting = ref(false)

onMounted(async () => {
  if (isEditing.value && costumeId.value) {
    loading.value = true
    await costumeStore.fetchCostumeById(costumeId.value)
    if (costumeStore.currentCostume) {
      form.value = { ...costumeStore.currentCostume }
    }
    loading.value = false
  }
})

const handleSubmit = async () => {
  submitting.value = true
  try {
    if (isEditing.value) {
      await costumeStore.updateCostume(costumeId.value, form.value)
    } else {
      await costumeStore.createCostume(form.value)
    }
    router.push('/costumes')
  } catch (error) {
    alert('Error saving costume: ' + error.message)
  } finally {
    submitting.value = false
  }
}
</script>
