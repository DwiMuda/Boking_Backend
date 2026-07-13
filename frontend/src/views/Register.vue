<template>
  <div class="auth-wrap">
    <div class="auth-main">
      <div class="auth-card">
        <h2>Daftar Akun</h2>

        <div v-if="authStore.error" class="alert alert-error">{{ authStore.error }}</div>

        <form @submit.prevent="handleRegister">
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input v-model="nama" placeholder="Nama Anda" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="email" type="email" placeholder="email@contoh.com" required />
          </div>
          <div class="form-group">
            <label>No. Telepon</label>
            <input v-model="no_telp" placeholder="0812xxxxxxxx" />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input v-model="password" type="password" placeholder="Min. 6 karakter" required />
          </div>
          <div class="form-group">
            <label>Konfirmasi Password</label>
            <input v-model="confirmPassword" type="password" placeholder="Ulangi password" required />
          </div>
          <button class="btn btn-primary btn-full" :disabled="authStore.loading">
            <AppSpinner v-if="authStore.loading" variant="ring" size="sm" />
            {{ authStore.loading ? 'Memproses...' : 'Daftar' }}
          </button>
        </form>

        <div class="auth-link">
          Sudah punya akun? <router-link to="/login">Masuk</router-link>
        </div>
      </div>
    </div>

    <div class="auth-side">
      <div class="auth-side-content">
        <div class="auth-side-icon">
          <TreePineIcon :size="40" />
        </div>
        <h2>Bergabunglah dengan Kami</h2>
        <p>Daftar sekarang untuk booking kamar, spa, dan dining di Tropical Resort.</p>
        <div class="auth-side-decor">
          <div class="decor-circle c1"></div>
          <div class="decor-circle c2"></div>
          <div class="decor-circle c3"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import { TreePineIcon } from '@lucide/vue'

const authStore = useAuthStore()
const router = useRouter()

const nama = ref('')
const email = ref('')
const no_telp = ref('')
const password = ref('')
const confirmPassword = ref('')

async function handleRegister() {
  authStore.clearError()
  if (password.value !== confirmPassword.value) {
    authStore.error = 'Password dan konfirmasi password tidak cocok'
    return
  }
  try {
    await authStore.register(nama.value, email.value, password.value, no_telp.value)
    router.push('/verify-email')
  } catch {}
}
</script>

<style scoped>
.auth-wrap {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: calc(100vh - 64px);
}

.auth-side {
  background-image: url('/images/aerial-beach.jpg');
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem 2rem;
  position: relative;
  overflow: hidden;
}

.auth-side::before {
  content: '';
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
}

.auth-side-content {
  text-align: center;
  position: relative;
  z-index: 1;
  max-width: 360px;
}

.auth-side-icon {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-full);
  background: rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  color: var(--accent-gold);
}

.auth-side-content h2 {
  color: var(--text-on-dark);
  font-size: var(--text-2xl);
  margin-bottom: 0.75rem;
}

.auth-side-content p {
  color: rgba(255, 255, 255, 0.6);
  line-height: var(--leading-relaxed);
}

.auth-side-decor {
  position: absolute;
  inset: 0;
  pointer-events: none;
  overflow: hidden;
}

.decor-circle {
  position: absolute;
  border-radius: var(--radius-full);
  border: 1px solid rgba(255, 255, 255, 0.06);
}

.c1 { width: 300px; height: 300px; top: -80px; left: -80px; }
.c2 { width: 200px; height: 200px; bottom: -40px; right: -40px; }
.c3 { width: 150px; height: 150px; top: 20%; left: 10%; border-color: rgba(201, 161, 92, 0.1); }

.auth-main {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.auth-card {
  width: 100%;
  max-width: 400px;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 2rem;
}

.auth-card h2 {
  font-size: var(--text-2xl);
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .auth-wrap { grid-template-columns: 1fr; }
  .auth-side { display: none; }
}
</style>
