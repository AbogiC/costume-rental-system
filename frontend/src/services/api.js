import axios from 'axios'

const API_BASE_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
})

export const costumeService = {
  async getAllCostumes() {
    const response = await api.get('/costumes.php')
    return response.data
  },

  async getCostumeById(id) {
    const response = await api.get(`/costumes.php?id=${id}`)
    return response.data
  },

  async createCostume(costumeData) {
    const response = await api.post('/costumes.php', costumeData)
    return response.data
  },

  async updateCostume(id, costumeData) {
    const response = await api.put(`/costumes.php?id=${id}`, costumeData)
    return response.data
  },

  async deleteCostume(id) {
    const response = await api.delete(`/costumes.php?id=${id}`)
    return response.data
  },
}

export const rentalService = {
  async createRental(rentalData) {
    const response = await api.post('/rentals.php', rentalData)
    return response.data
  },

  async getAllRentals() {
    const response = await api.get('/rentals.php')
    return response.data
  },

  async updateRentalStatus(rentalId, status) {
    const response = await api.post('/rentals.php', {
      action: 'update_status',
      id: rentalId,
      status: status,
    })
    return response.data
  },
}

export const authService = {
  async login(credentials) {
    const response = await api.post('/auth.php', { action: 'login', ...credentials })
    return response.data
  },

  async register(userData) {
    const response = await api.post('/auth.php', { action: 'register', ...userData })
    return response.data
  },
}

export default api
