<template>
  <div class="profile-page">
    <div v-if="!user" class="loading">
      <AppSpinner variant="ring" size="lg" />
      <span>Memuat profil...</span>
    </div>

    <div v-else class="profile-card">
      <div class="profile-header">
        <div class="profile-avatar">
          <UserIcon :size="32" />
        </div>
        <div>
          <h2>{{ user.nama }}</h2>
          <p class="text-muted fs-0-9">{{ user.email }}</p>
        </div>
        <span class="badge" :class="user.role === 'admin' ? 'badge-confirmed' : 'badge-active'">{{ user.role === 'admin' ? 'Admin' : 'User' }}</span>
      </div>

      <div v-if="error" class="alert alert-error">{{ error }}</div>
      <div v-if="success" class="alert alert-success">{{ success }}</div>

      <div class="profile-form">
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
          <input v-model="form.no_telp" placeholder="Belum diisi" />
        </div>
        <button class="btn btn-primary" :disabled="saving" @click="handleSave">
          <AppSpinner v-if="saving" variant="ring" size="sm" />
          {{ saving ? 'Menyimpan...' : 'Simpan Perubahan' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { UserIcon } from '@lucide/vue'
import { getCurrentInstance } from 'vue'

const { proxy } = getCurrentInstance()
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
  if (!form.value.nama.trim()) { error.value = 'Nama tidak boleh kosong'; return }
  saving.value = true
  try {
    await auth.updateProfile({ nama: form.value.nama.trim(), no_telp: form.value.no_telp.trim() })
    user.value = auth.user
    success.value = 'Profil berhasil diupdate'
    proxy.$toast('Profil berhasil diupdate', 'success')
  } catch (e) {
    error.value = e.message || 'Gagal menyimpan profil'
    proxy.$toast(e.message || 'Gagal menyimpan profil', 'error')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.profile-page {
  max-width: 500px;
  margin: 0 auto;
}

.profile-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  border-bottom: 1px solid var(--border-subtle);
}

.profile-avatar {
  width: 56px;
  height: 56px;
  border-radius: var(--radius-full);
  background: var(--accent-primary-muted);
  color: var(--accent-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.profile-header h2 {
  font-size: var(--text-lg);
}

.profile-form {
  padding: 1.5rem 2rem 2rem;
}
</style>
