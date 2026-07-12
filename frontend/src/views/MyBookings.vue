<template>
  <div class="container">
    <h2 class="mb-1">Booking Saya</h2>

    <div class="filter-bar">
      <select v-model="tipeFilter" @change="resetAndFetch">
        <option value="">Semua Tipe</option>
        <option value="service">Spa &amp; Dining</option>
        <option value="room">Kamar</option>
      </select>
    </div>

    <AppSkeleton v-if="loading" type="text" :count="4" />

    <div v-else-if="bookings.length === 0">
      <AppEmptyState type="booking" message="Belum ada booking. Yuk mulai booking pertamamu!">
        <template #action>
          <router-link to="/services" class="btn btn-primary btn-sm">Buat Booking</router-link>
        </template>
      </AppEmptyState>
    </div>

    <div v-else class="booking-list">
      <div v-for="b in bookings" :key="b.id" class="booking-card">
        <div class="booking-main">
          <div class="booking-info">
            <span class="badge mb-0-5" :class="b.tipe_booking === 'room' ? 'badge-gold' : b.kategori === 'spa' ? 'badge-active' : 'badge-gold'" style="font-size:0.65rem;">
              {{ b.tipe_booking === 'room' ? 'KAMAR' : (b.kategori || b.tipe_booking || 'LAYANAN').toUpperCase() }}
            </span>
            <h3>{{ b.service_nama || b.room_type_nama || 'Booking' }}</h3>
            <div class="booking-meta">
              <CalendarIcon :size="14" />
              <span v-if="b.tipe_booking === 'room'">{{ b.check_in }} s.d {{ b.check_out }}</span>
              <span v-else>{{ b.tgl_booking }} {{ b.jam_booking?.substring(0, 5) }}</span>
            </div>
            <div v-if="b.nomor_kamar" class="booking-meta">
              <span class="text-muted">Kamar {{ b.nomor_kamar }}</span>
            </div>
            <div class="booking-price">Rp {{ formatHarga(b.total_harga) }}</div>
          </div>
          <div class="booking-status">
            <span class="badge" :class="'badge-' + b.status">{{ statusLabel(b.status) }}</span>
          </div>
        </div>
        <div class="booking-actions">
          <router-link :to="`/booking/${b.id}/confirmation`" class="btn btn-ghost btn-sm">Detail</router-link>
          <button v-if="b.status === 'pending'" class="btn btn-danger btn-sm" @click="confirmCancel(b.id)">Batalkan</button>
        </div>
      </div>

      <AppPagination v-model="page" :total-pages="totalPages" @update:model-value="goTo" />
    </div>

    <AppConfirm :open="confirmOpen" message="Yakin ingin membatalkan booking ini?" action-label="Batalkan Booking" @confirm="doCancel" @cancel="confirmOpen = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { getMyBookings, cancelBooking } from '../api'
import { CalendarIcon } from '@lucide/vue'

const { proxy } = getCurrentInstance()

const bookings = ref([])
const loading = ref(true)
const page = ref(1)
const totalPages = ref(1)
const cancelId = ref(null)
const confirmOpen = ref(false)
const tipeFilter = ref('')

async function fetchBookings() {
  loading.value = true
  try {
    const res = await getMyBookings(page.value)
    bookings.value = res.data.items
    totalPages.value = res.data.pagination.total_pages
  } catch (e) {
    proxy.$toast(e.message || 'Gagal memuat data booking', 'error')
  } finally {
    loading.value = false
  }
}

function resetAndFetch() {
  page.value = 1
  fetchBookings()
}

function goTo(p) {
  page.value = p
  fetchBookings()
}

onMounted(fetchBookings)

function confirmCancel(id) {
  cancelId.value = id
  confirmOpen.value = true
}

async function doCancel() {
  confirmOpen.value = false
  try {
    await cancelBooking(cancelId.value)
    bookings.value = bookings.value.map(b => b.id === cancelId.value ? { ...b, status: 'cancelled' } : b)
    proxy.$toast('Booking berhasil dibatalkan', 'success')
  } catch (e) {
    proxy.$toast(e.message || 'Gagal membatalkan booking', 'error')
  }
}

function statusLabel(status) {
  const map = { pending: 'Menunggu', confirmed: 'Dikonfirmasi', completed: 'Selesai', cancelled: 'Dibatalkan' }
  return map[status] || status
}

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>

<style scoped>
.booking-list { display: flex; flex-direction: column; gap: 0.75rem; }

.booking-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 1.25rem 1.5rem;
  transition: all var(--duration-normal) var(--ease-smooth);
}

.booking-card:hover { border-color: var(--border-default); }

.booking-main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.booking-info h3 { font-size: var(--text-base); margin-bottom: 0.35rem; }

.booking-meta {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  flex-wrap: wrap;
  font-size: var(--text-sm);
  color: var(--text-muted);
  margin-bottom: 0.25rem;
}

.booking-meta svg { color: var(--accent-primary); }

.booking-price {
  font-family: var(--font-heading);
  font-weight: var(--weight-bold);
  color: var(--accent-warm);
  font-size: var(--text-sm);
}

.booking-actions {
  display: flex;
  gap: 0.5rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-subtle);
}
</style>
