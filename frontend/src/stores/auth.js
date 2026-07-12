import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { login as apiLogin, register as apiRegister, getProfile, logout as apiLogout, updateProfile as apiUpdateProfile, verifyEmail as apiVerifyEmail, resendOtp as apiResendOtp } from '../api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))
  const token = ref(localStorage.getItem('token') || '')
  const registeredEmail = ref('')
  const error = ref('')
  const loading = ref(false)

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const userName = computed(() => user.value?.nama || '')

  async function login(email, password) {
    try {
      loading.value = true
      error.value = ''
      const res = await apiLogin(email, password)
      user.value = res.data
      token.value = res.data.token
      localStorage.setItem('user', JSON.stringify(res.data))
      localStorage.setItem('token', res.data.token)
      return res
    } catch (e) {
      error.value = e.message || 'Login gagal'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function register(nama, email, password, no_telp) {
    try {
      loading.value = true
      error.value = ''
      const res = await apiRegister(nama, email, password, no_telp)
      registeredEmail.value = email
      return res
    } catch (e) {
      error.value = e.message || 'Registrasi gagal'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function verifyEmail(email, otp) {
    try {
      loading.value = true
      error.value = ''
      const res = await apiVerifyEmail(email, otp)
      user.value = res.data
      token.value = res.data.token
      localStorage.setItem('user', JSON.stringify(res.data))
      localStorage.setItem('token', res.data.token)
      registeredEmail.value = ''
      return res
    } catch (e) {
      error.value = e.message || 'Verifikasi gagal'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function resendOtp(email) {
    try {
      error.value = ''
      return await apiResendOtp(email)
    } catch (e) {
      error.value = e.message || 'Gagal kirim ulang OTP'
      throw e
    }
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
    error.value = ''
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  async function updateProfile(data) {
    try {
      error.value = ''
      const res = await apiUpdateProfile(data)
      user.value = res.data
      localStorage.setItem('user', JSON.stringify(res.data))
      return res
    } catch (e) {
      error.value = e.message || 'Gagal update profil'
      throw e
    }
  }

  const clearError = () => { error.value = '' }

  return { user, token, registeredEmail, isLoggedIn, isAdmin, userName, loading, error, login, register, verifyEmail, resendOtp, fetchProfile, logout, updateProfile, clearError }
})
