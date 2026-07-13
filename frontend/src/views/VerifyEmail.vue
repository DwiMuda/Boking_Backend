<template>
  <div class="verify-wrap">
    <div class="verify-card">
      <div v-if="verified" class="verify-success">
        <div class="verify-success-icon">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2>Email Berhasil Diverifikasi!</h2>
        <p>Mengalihkan ke halaman utama...</p>
      </div>

      <template v-else>
        <div class="verify-header">
          <div class="verify-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          </div>
          <h2>Cek Email Anda</h2>
          <p class="text-muted">Kode verifikasi telah dikirim ke <strong>{{ email }}</strong></p>
        </div>

        <div v-if="error" class="alert alert-error">{{ error }}</div>
        <div v-if="successMsg" class="alert alert-success">{{ successMsg }}</div>

        <div class="otp-inputs">
          <input
            v-for="(d, i) in 6"
            :key="i"
            ref="otpRefs"
            v-model="digits[i]"
            type="text"
            maxlength="1"
            inputmode="numeric"
            pattern="[0-9]"
            autocomplete="one-time-code"
            class="otp-digit"
            :class="{ filled: digits[i] }"
            @input="onOtpInput(i)"
            @keydown.backspace="onOtpBackspace(i)"
            @paste="onOtpPaste"
          />
        </div>

        <button class="btn btn-primary btn-full" :disabled="loading || digits.join('').length < 6" @click="handleVerify">
          <AppSpinner v-if="loading" variant="ring" size="sm" />
          {{ loading ? 'Memverifikasi...' : 'Verifikasi' }}
        </button>

        <div class="resend-section">
          <p v-if="cooldown > 0" class="text-muted">Kirim ulang dalam {{ cooldown }} detik</p>
          <button v-else class="btn-resend" :disabled="resending" @click="handleResend">
            {{ resending ? 'Mengirim...' : 'Kirim ulang kode' }}
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { getCurrentInstance } from 'vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const { proxy } = getCurrentInstance()

const email = ref(route.query.email || auth.registeredEmail || '')
const digits = reactive(['', '', '', '', '', ''])
const otpRefs = ref([])
const loading = ref(false)
const resending = ref(false)
const error = ref('')
const successMsg = ref('')
const cooldown = ref(0)
const verified = ref(false)
let cooldownTimer = null

if (!email.value) {
  router.push('/register')
}

function onOtpInput(index) {
  digits[index] = digits[index].replace(/\D/g, '').slice(0, 1)
  if (digits[index] && index < 5) {
    otpRefs.value[index + 1]?.focus()
  }
}

function onOtpBackspace(index) {
  if (!digits[index] && index > 0) {
    otpRefs.value[index - 1]?.focus()
  }
}

function onOtpPaste(e) {
  e.preventDefault()
  const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6)
  for (let i = 0; i < 6; i++) {
    digits[i] = paste[i] || ''
  }
  if (paste.length === 6) {
    otpRefs.value[5]?.focus()
  }
}

async function handleVerify() {
  error.value = ''
  const otp = digits.join('')
  if (otp.length < 6) return
  loading.value = true
  try {
    await auth.verifyEmail(email.value, otp)
    verified.value = true
    setTimeout(() => router.push('/'), 1500)
  } catch (e) {
    error.value = e.message || 'Kode tidak valid'
    digits.fill('')
    otpRefs.value[0]?.focus()
  } finally {
    loading.value = false
  }
}

async function handleResend() {
  error.value = ''
  successMsg.value = ''
  resending.value = true
  try {
    await auth.resendOtp(email.value)
    successMsg.value = 'Kode dikirim ulang'
    startCooldown()
  } catch (e) {
    error.value = e.message || 'Gagal mengirim ulang'
  } finally {
    resending.value = false
  }
}

function startCooldown() {
  cooldown.value = 30
  if (cooldownTimer) clearInterval(cooldownTimer)
  cooldownTimer = setInterval(() => {
    cooldown.value--
    if (cooldown.value <= 0) {
      clearInterval(cooldownTimer)
      cooldownTimer = null
    }
  }, 1000)
}

onMounted(() => {
  setTimeout(() => otpRefs.value[0]?.focus(), 300)
})
</script>

<style scoped>
.verify-wrap {
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.verify-card {
  width: 100%;
  max-width: 440px;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  padding: 2.5rem 2rem;
  text-align: center;
}

.verify-header {
  margin-bottom: 2rem;
}

.verify-icon {
  width: 64px;
  height: 64px;
  border-radius: var(--radius-full);
  background: var(--accent-primary-muted);
  color: var(--accent-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.verify-header h2 {
  font-size: var(--text-2xl);
  margin-bottom: 0.5rem;
}

.otp-inputs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.otp-digit {
  width: 48px;
  height: 56px;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 700;
  font-family: var(--font-heading);
  background: var(--bg-elevated);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  outline: none;
  transition: all 0.15s ease;
}

.otp-digit:focus {
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 3px var(--accent-primary-muted);
}

.otp-digit.filled {
  border-color: var(--accent-primary);
  background: var(--accent-primary-muted);
}

.resend-section {
  margin-top: 1.25rem;
}

.btn-resend {
  background: none;
  border: none;
  color: var(--accent-primary);
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: 600;
  cursor: pointer;
  padding: 0;
}

.btn-resend:hover {
  color: var(--accent-primary-hover);
  text-decoration: underline;
}

.btn-resend:disabled {
  color: var(--text-muted);
  cursor: not-allowed;
  text-decoration: none;
}

.verify-success-icon {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-full);
  background: var(--semantic-success-bg);
  color: var(--semantic-success);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
}

.verify-success h2 {
  color: var(--semantic-success);
  margin-bottom: 0.5rem;
}
</style>
