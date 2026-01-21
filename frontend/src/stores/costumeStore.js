import { defineStore } from 'pinia'
import { costumeService } from '@/services/api'

export const useCostumeStore = defineStore('costume', {
  state: () => ({
    costumes: [],
    currentCostume: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchCostumes() {
      this.loading = true
      this.error = null
      try {
        this.costumes = await costumeService.getAllCostumes()
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async fetchCostumeById(id) {
      this.loading = true
      this.error = null
      try {
        this.currentCostume = await costumeService.getCostumeById(id)
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    },

    async createCostume(costumeData) {
      this.loading = true
      this.error = null
      try {
        await costumeService.createCostume(costumeData)
        await this.fetchCostumes()
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async updateCostume(id, costumeData) {
      this.loading = true
      this.error = null
      try {
        await costumeService.updateCostume(id, costumeData)
        await this.fetchCostumes()
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async deleteCostume(id) {
      this.loading = true
      this.error = null
      try {
        await costumeService.deleteCostume(id)
        await this.fetchCostumes()
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },
  },

  getters: {
    availableCostumes: (state) =>
      state.costumes.filter((costume) => costume.quantity_available > 0),

    getCostumeById: (state) => (id) => state.costumes.find((costume) => costume.id == id),
  },
})
