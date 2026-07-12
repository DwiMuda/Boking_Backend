<template>
  <div class="auth-container">
    <div class="card">
      <h2>Daftar Akun</h2>
      <div v-if="error" class="alert alert-error">{{ error }}</div>
      <form @submit.prevent="handleRegister">
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input v-model="nama" type="text" placeholder="Nama anda" required />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input v-model="email" type="email" placeholder="email@example.com" required />
        </div>
        <div class="form-group">
          <label>No. Telepon</label>
          <input v-model="noTelp" type="text" placeholder="08123456789" />
        </div>
        <div class="form-group">
          <label>Password</label>
          <input v-model="password" type="password" placeholder="Minimal 6 karakter" required />
        </div>
        <button type="submit" class="btn btn-primary btn-full" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Daftar' }}
        </button>
      </form>
      <div class="auth-link">
        Sudah punya akun? <router-link to="/login">Login disini</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()
const nama = ref('')
const email = ref('')
const noTelp = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleRegister() {
  error.value = ''
  if (!nama.value.trim()) {
    error.value = 'Nama harus diisi'
    return
  }
  if (!email.value.includes('@')) {
    error.value = 'Format email tidak valid'
    return
  }
  if (password.value.length < 6) {
    error.value = 'Password minimal 6 karakter'
    return
  }
  loading.value = true
  try {
    await auth.register(nama.value, email.value, password.value, noTelp.value)
    router.push('/')
  } catch (e) {
    error.value = e.message || 'Registrasi gagal'
  } finally {
    loading.value = false
  }
}
</script>
