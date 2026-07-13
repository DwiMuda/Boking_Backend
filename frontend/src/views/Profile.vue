<template>
  <div class="profile-page">
    <div v-if="!user" class="loading">
      <AppSpinner variant="ring" size="lg" />
      <span>Memuat profil...</span>
    </div>

    <div v-else class="profile-wrap">
      <div class="profile-header-card">
        <div class="profile-avatar">
          {{ user.nama?.charAt(0) || 'U' }}
        </div>
        <div class="profile-info">
          <h2>{{ user.nama }}</h2>
          <p class="profile-email">{{ user.email }}</p>
          <div class="profile-meta">
            <span class="badge" :class="user.role === 'admin' ? 'badge-confirmed' : 'badge-active'">
              {{ user.role === 'admin' ? 'Admin' : 'User' }}
            </span>
            <span class="member-since">
              <CalendarIcon :size="12" />
              {{ memberSince }}
            </span>
          </div>
        </div>
      </div>

      <div class="profile-tabs">
        <button
          class="tab-btn"
          :class="{ active: activeTab === 'info' }"
          @click="activeTab = 'info'"
        >
          <UserIcon :size="16" />
          Info Profil
        </button>
        <button
          class="tab-btn"
          :class="{ active: activeTab === 'bookings' }"
          @click="activeTab = 'bookings'; fetchBookings()"
        >
          <CalendarCheckIcon :size="16" />
          Riwayat Booking
        </button>
      </div>

      <div v-if="error" class="alert alert-error">{{ error }}</div>
      <div v-if="success" class="alert alert-success">{{ success }}</div>

      <div v-if="activeTab === 'info'" class="profile-section">
        <div class="profile-form">
          <div class="form-group">
            <label>Nama Lengkap</label>
            <input v-model="form.nama" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input :value="user.email" readonly class="input-readonly" />
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

      <div v-else class="profile-section">
        <div v-if="loadingBookings" class="loading">
          <AppSpinner variant="ring" size="sm" />
          <span>Memuat booking...</span>
        </div>
        <div v-else-if="bookings.length === 0" class="empty-state">
          <CalendarCheckIcon :size="40" />
          <p>Belum ada booking</p>
          <router-link to="/services" class="btn btn-primary">Lihat Layanan</router-link>
        </div>
        <div v-else class="booking-list">
          <div v-for="b in bookings" :key="b.id" class="booking-item">
            <div class="booking-item-header">
              <span class="booking-type">{{ b.tipe_booking === 'room' ? '🏨' : '💆' }} {{ b.service_nama || b.room_type_nama || 'Booking' }}</span>
              <span class="badge" :class="'badge-' + b.status">{{ statusLabel(b.status) }}</span>
            </div>
            <div class="booking-item-body">
              <div class="booking-item-detail">
                <CalendarIcon :size="14" />
                <span>{{ b.tgl_booking || b.check_in }} {{ b.jam_booking ? b.jam_booking.slice(0,5) : '' }}</span>
              </div>
              <div class="booking-item-detail">
                <DollarSignIcon :size="14" />
                <span>Rp {{ numberFormat(b.total_harga) }}</span>
              </div>
            </div>
            <div v-if="b.status === 'pending'" class="booking-item-action">
              <button class="btn-cancel" @click="handleCancel(b.id)">Batalkan</button>
            </div>
          </div>
          <div v-if="pagination.total_pages > 1" class="pagination">
            <button :disabled="page <= 1" @click="page--; fetchBookings()" class="btn-page">←</button>
            <span class="page-info">{{ page }} / {{ pagination.total_pages }}</span>
            <button :disabled="page >= pagination.total_pages" @click="page++; fetchBookings()" class="btn-page">→</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { UserIcon, CalendarIcon, CalendarCheckIcon, DollarSignIcon } from '@lucide/vue'
import { getCurrentInstance } from 'vue'
import { getMyBookings, cancelBooking } from '../api'

const { proxy } = getCurrentInstance()
const auth = useAuthStore()
const user = ref(null)
const form = ref({ nama: '', no_telp: '' })
const saving = ref(false)
const error = ref('')
const success = ref('')
const activeTab = ref('info')
const bookings = ref([])
const loadingBookings = ref(false)
const page = ref(1)
const pagination = ref({})

const memberSince = computed(() => {
  if (!user.value?.created_at) return ''
  const d = new Date(user.value.created_at)
  return `Sejak ${d.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}`
})

function statusLabel(s) {
  const labels = { pending: 'Menunggu', confirmed: 'Dikonfirmasi', completed: 'Selesai', cancelled: 'Dibatalkan' }
  return labels[s] || s
}

function numberFormat(n) {
  return Number(n || 0).toLocaleString('id-ID')
}

async function fetchBookings() {
  loadingBookings.value = true
  try {
    const res = await getMyBookings(page.value)
    bookings.value = res.data?.items || []
    pagination.value = res.data?.pagination || {}
  } catch {
    bookings.value = []
  } finally {
    loadingBookings.value = false
  }
}

async function handleCancel(id) {
  if (!confirm('Yakin ingin membatalkan booking ini?')) return
  try {
    await cancelBooking(id)
    proxy.$toast('Booking dibatalkan', 'success')
    fetchBookings()
  } catch (e) {
    proxy.$toast(e.message || 'Gagal membatalkan', 'error')
  }
}

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
  max-width: 640px;
  margin: 0 auto;
}

.profile-header-card {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  padding: 1.5rem;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  margin-bottom: 1rem;
}

.profile-avatar {
  width: 72px;
  height: 72px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  font-family: var(--font-heading);
  flex-shrink: 0;
  box-shadow: 0 0 30px rgba(45, 90, 69, 0.2);
}

.profile-info h2 {
  font-size: var(--text-xl);
}

.profile-email {
  color: var(--text-muted);
  font-size: var(--text-sm);
  margin: 0.15rem 0 0.5rem;
}

.profile-meta {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.member-since {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  color: var(--text-muted);
  font-size: var(--text-xs);
}

.profile-tabs {
  display: flex;
  gap: 0.25rem;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 0.25rem;
  margin-bottom: 1rem;
}

.tab-btn {
  flex: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.65rem 1rem;
  border-radius: var(--radius-sm);
  border: none;
  background: none;
  color: var(--text-secondary);
  font-family: var(--font-body);
  font-size: var(--text-sm);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.tab-btn.active {
  background: var(--accent-primary-muted);
  color: var(--accent-primary);
}

.tab-btn:hover:not(.active) {
  background: var(--bg-hover);
}

.profile-section {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  padding: 1.5rem;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.input-readonly {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Booking list */
.booking-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.booking-item {
  background: var(--bg-elevated);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 1rem;
  transition: all 0.15s ease;
}

.booking-item:hover {
  border-color: var(--border-default);
}

.booking-item-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.booking-type {
  font-weight: 600;
  font-size: var(--text-sm);
}

.booking-item-body {
  display: flex;
  gap: 1.5rem;
  font-size: var(--text-sm);
  color: var(--text-secondary);
}

.booking-item-detail {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
}

.booking-item-action {
  margin-top: 0.5rem;
}

.btn-cancel {
  background: none;
  border: 1px solid var(--semantic-danger);
  color: var(--semantic-danger);
  padding: 0.3rem 0.75rem;
  border-radius: var(--radius-sm);
  font-size: var(--text-xs);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
}

.btn-cancel:hover {
  background: var(--semantic-danger-bg);
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: var(--text-muted);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-top: 1rem;
}

.btn-page {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-full);
  border: 1px solid var(--border-subtle);
  background: var(--bg-elevated);
  color: var(--text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.btn-page:hover:not(:disabled) {
  border-color: var(--accent-primary);
  color: var(--accent-primary);
}

.btn-page:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.page-info {
  font-size: var(--text-sm);
  color: var(--text-muted);
}
</style>
