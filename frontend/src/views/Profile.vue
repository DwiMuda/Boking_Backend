<template>
  <div class="auth-container">
    <div class="card">
      <h2>Profil Saya</h2>
      <div v-if="user">
        <div class="form-group">
          <label>Nama</label>
          <input :value="user.nama" readonly />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input :value="user.email" readonly />
        </div>
        <div class="form-group">
          <label>No. Telepon</label>
          <input :value="user.no_telp || '-'" readonly />
        </div>
        <div class="form-group">
          <label>Role</label>
          <input :value="user.role" readonly />
        </div>
      </div>
      <div v-else class="loading">Memuat profil...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getProfile } from '../api'

const user = ref(null)

onMounted(async () => {
  try {
    const res = await getProfile()
    user.value = res.data
  } catch (e) {
    console.error('Gagal memuat profil', e)
  }
})
</script>
