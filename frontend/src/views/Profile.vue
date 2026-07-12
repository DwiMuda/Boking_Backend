<template>
  <div class="auth-container">
    <div class="card">
      <h2>Profil Saya</h2>
      <div v-if="user">
        <div class="form-group">
          <label>Nama</label>
          <input v-model="form.nama" />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input :value="user.email" readonly />
        </div>
        <div class="form-group">
          <label>No. Telepon</label>
          <input v-model="form.no_telp" />
        </div>
        <div class="form-group">
          <label>Role</label>
          <input :value="user.role" readonly />
        </div>
        <div v-if="error" class="alert alert-danger">{{ error }}</div>
        <div v-if="success" class="alert alert-success">{{ success }}</div>
        <div class="form-group">
          <button class="btn btn-primary" :disabled="saving" @click="handleSave">
            {{ saving ? 'Menyimpan...' : 'Simpan' }}
          </button>
        </div>
      </div>
      <div v-else class="loading">Memuat profil...</div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const user = ref(null)
const form = ref({ nama: '', no_telp: '' })
const saving = ref(false)
const error = ref('')
const success = ref('')

onMounted(async () => {
  await auth.fetchProfile()
  user.value = auth.user
  if (user.value) {
    form.value = { nama: user.value.nama || '', no_telp: user.value.no_telp || '' }
  }
})

async function handleSave() {
  error.value = ''
  success.value = ''
  if (!form.value.nama.trim()) {
    error.value = 'Nama tidak boleh kosong'
    return
  }
  saving.value = true
  try {
    await auth.updateProfile({ nama: form.value.nama.trim(), no_telp: form.value.no_telp.trim() })
    user.value = auth.user
    success.value = 'Profil berhasil diupdate'
  } catch (e) {
    error.value = e.message || 'Gagal menyimpan profil'
  } finally {
    saving.value = false
  }
}
</script>
