<template>
  <div class="container">
    <div class="flex items-center gap-3 mb-1">
      <button class="btn btn-ghost btn-sm" @click="$router.push('/admin')">
        <ArrowLeftIcon :size="18" />
        Kembali ke Dashboard
      </button>
      <h2 class="mb-0">Kelola Booking</h2>
    </div>

    <div class="filter-bar">
      <input v-model="search" placeholder="Cari user/layanan..." @input="onSearch" />
      <select v-model="statusFilter" @change="resetAndFetch">
        <option value="">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="confirmed">Dikonfirmasi</option>
        <option value="completed">Selesai</option>
        <option value="cancelled">Dibatalkan</option>
      </select>
      <select v-model="tipeFilter" @change="resetAndFetch">
        <option value="">Semua Tipe</option>
        <option value="room">Kamar</option>
        <option value="service">Spa &amp; Dining</option>
      </select>
    </div>

    <AppSkeleton v-if="loading" type="table-row" :count="5" />

    <div v-else-if="bookings.length === 0">
      <AppEmptyState type="search" message="Tidak ada booking ditemukan" />
    </div>

    <div v-else class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Tipe</th>
            <th>Item</th>
            <th>Tanggal/CI-CO</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in bookings" :key="b.id">
            <td>#{{ b.id }}</td>
            <td>{{ b.user_nama }}</td>
            <td><span class="badge badge-sm" :class="b.tipe_booking === 'room' ? 'badge-gold' : 'badge-active'">{{ b.tipe_booking }}</span></td>
            <td>{{ b.service_nama || b.room_type_nama }}</td>
            <td>{{ b.tipe_booking === 'room' ? (b.check_in + ' s.d ' + b.check_out) : (b.tgl_booking + ' ' + (b.jam_booking ? b.jam_booking.substring(0, 5) : '')) }}</td>
            <td>Rp {{ formatHarga(b.total_harga) }}</td>
            <td><span class="badge" :class="'badge-' + b.status">{{ statusLabel(b.status) }}</span></td>
            <td>
              <select v-if="b.status !== 'cancelled'" :value="b.status" @change="updateStatus(b.id, $event.target.value)" class="status-select">
                <option value="pending">Pending</option>
                <option value="confirmed">Konfirmasi</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Batalkan</option>
              </select>
              <span v-else class="text-muted fs-0-9">—</span>
            </td>
          </tr>
        </tbody>
      </table>

      <AppPagination v-model="page" :total-pages="totalPages" @update:model-value="fetchBookings" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { getAdminBookings, updateBookingStatus } from '../../api'
import { ArrowLeftIcon } from '@lucide/vue'

const { proxy } = getCurrentInstance()

const bookings = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const tipeFilter = ref('')
const page = ref(1)
const totalPages = ref(1)

function getParams() {
  const params = { page: page.value }
  if (statusFilter.value) params.status = statusFilter.value
  if (tipeFilter.value) params.tipe = tipeFilter.value
  if (search.value) params.search = search.value
  return params
}

async function fetchBookings() {
  loading.value = true
  try {
    const res = await getAdminBookings(getParams())
    bookings.value = res.data?.items || []
    totalPages.value = res.data?.pagination?.total_pages || 1
  } catch (e) {
    proxy.$toast(e.message || 'Gagal memuat data', 'error')
  } finally {
    loading.value = false
  }
}

function resetAndFetch() {
  page.value = 1
  fetchBookings()
}

let debounceTimer
function onSearch() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    page.value = 1
    fetchBookings()
  }, 300)
}

onMounted(fetchBookings)

async function updateStatus(id, status) {
  try {
    await updateBookingStatus(id, status)
    bookings.value = bookings.value.map(b => b.id === id ? { ...b, status } : b)
    proxy.$toast('Status booking berhasil diupdate', 'success')
  } catch (e) {
    proxy.$toast(e.message || 'Gagal update status', 'error')
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