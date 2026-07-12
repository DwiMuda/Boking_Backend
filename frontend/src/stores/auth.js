import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { login as apiLogin, register as apiRegister, getProfile, logout as apiLogout, updateProfile as apiUpdateProfile } from '../api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || '')

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const userName = computed(() => user.value?.nama || '')

  async function login(email, password) {
    const res = await apiLogin(email, password)
    user.value = res.data
    token.value = res.data.token
    localStorage.setItem('user', JSON.stringify(res.data))
    localStorage.setItem('token', res.data.token)
    return res
  }

  async function register(nama, email, password, no_telp) {
    const res = await apiRegister(nama, email, password, no_telp)
    user.value = res.data
    token.value = res.data.token
    localStorage.setItem('user', JSON.stringify(res.data))
    localStorage.setItem('token', res.data.token)
    return res
  }

  async function fetchProfile() {
    try {
      const res = await getProfile()
      user.value = res.data
      localStorage.setItem('user', JSON.stringify(res.data))
    } catch {
      logout()
    }
  }

  async function logout() {
    try { await apiLogout() } catch {}
    user.value = null
    token.value = ''
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  async function updateProfile(data) {
    const res = await apiUpdateProfile(data)
    user.value = res.data
    localStorage.setItem('user', JSON.stringify(res.data))
    return res
  }

  return { user, token, isLoggedIn, isAdmin, userName, login, register, fetchProfile, logout, updateProfile }
})
