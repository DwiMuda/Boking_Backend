import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost/Boking_Backend',
  headers: {
    'Content-Type': 'application/json'
  }
})

api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

api.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error.response?.data || { message: 'Network error' })
  }
)

export function login(email, password) {
  return api.post('/api/auth/login.php', { email, password })
}

export function register(nama, email, password, no_telp) {
  return api.post('/api/auth/register.php', { nama, email, password, no_telp })
}

export function getProfile() {
  return api.get('/api/auth/profile.php')
}

export function getServices() {
  return api.get('/api/services/list.php')
}

export function getServiceDetail(id) {
  return api.get(`/api/services/detail.php?id=${id}`)
}

export function createService(data) {
  return api.post('/api/services/create.php', data)
}

export function updateService(data) {
  return api.post('/api/services/update.php', data)
}

export function deleteService(id) {
  return api.post('/api/services/delete.php', { id })
}

export function checkAvailability(serviceId, tgl) {
  return api.get(`/api/bookings/check.php?service_id=${serviceId}&tgl=${tgl}`)
}

export function createBooking(data) {
  return api.post('/api/bookings/create.php', data)
}

export function getBookingDetail(id) {
  return api.get(`/api/bookings/detail.php?id=${id}`)
}

export function getMyBookings(page = 1) {
  return api.get(`/api/bookings/my_bookings.php?page=${page}`)
}

export function cancelBooking(id) {
  return api.post('/api/bookings/cancel.php', { id })
}

export function logout() {
  return api.post('/api/auth/logout.php')
}

export function updateProfile(data) {
  return api.post('/api/auth/update.php', data)
}

export function getAdminBookings(params = {}) {
  const query = new URLSearchParams(params).toString()
  return api.get(`/api/admin/bookings.php?${query}`)
}

export function updateBookingStatus(id, status) {
  return api.post('/api/admin/booking_update.php', { id, status })
}

export function getAdminDashboard() {
  return api.get('/api/admin/dashboard.php')
}

export function getAdminReports(params = {}) {
  const query = new URLSearchParams(params).toString()
  return api.get(`/api/admin/reports.php?${query}`)
}

// ── Room API ──
export function getRooms() {
  return api.get('/api/rooms/list.php')
}

export function getRoomDetail(id) {
  return api.get(`/api/rooms/detail.php?id=${id}`)
}

export function getAvailableRooms(check_in, check_out, kapasitas = 0) {
  return api.get(`/api/rooms/available.php?check_in=${check_in}&check_out=${check_out}&kapasitas=${kapasitas}`)
}

export function bookRoom(data) {
  return api.post('/api/rooms/book.php', data)
}

export function createRoomType(data) {
  return api.post('/api/rooms/create.php', data)
}

export function updateRoomType(data) {
  return api.post('/api/rooms/update.php', data)
}

export function deleteRoomType(id) {
  return api.post('/api/rooms/delete.php', { id })
}

// ── Services all (admin) ──
export function getAllServices() {
  return api.get('/api/services/list_all.php')
}

export default api
