<template>
  <div>
    <h2 class="mb-1">Kelola Booking</h2>

    <div class="filter-bar">
      <input v-model="search" placeholder="Cari user/layanan..." @input="onSearchInput" />
      <select v-model="statusFilter" @change="fetchBookings">
        <option value="">Semua Status</option>
        <option value="pending">Menunggu</option>
        <option value="confirmed">Dikonfirmasi</option>
        <option value="completed">Selesai</option>
        <option value="cancelled">Dibatalkan</option>
      </select>
    </div>

    <div v-if="loading" class="loading">Memuat data...</div>
    <div v-else-if="bookings.length === 0" class="empty-state">Tidak ada booking</div>
    <div v-else class="card overflow-x-auto">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User</th>
            <th>Layanan</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="b in bookings" :key="b.id">
            <td>#{{ b.id }}</td>
            <td>{{ b.user_nama }}</td>
            <td>{{ b.service_nama }}</td>
            <td>{{ b.tgl_booking }}</td>
            <td>{{ b.jam_booking.substring(0,5) }}</td>
            <td>Rp {{ formatHarga(b.total_harga) }}</td>
            <td><span class="badge" :class="'badge-' + b.status">{{ statusLabel(b.status) }}</span></td>
            <td>
              <select v-if="b.status !== 'cancelled'" :value="b.status" @change="updateStatus(b.id, $event.target.value)" class="status-select">
                <option value="pending">Pending</option>
                <option value="confirmed">Konfirmasi</option>
                <option value="completed">Selesai</option>
                <option value="cancelled">Batalkan</option>
              </select>
              <span v-else>-</span>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="totalPages > 1" class="pagination">
        <button :disabled="page <= 1" @click="goTo(page - 1)" class="btn btn-outline">Sebelumnya</button>
        <span v-for="p in visiblePages" :key="p">
          <span v-if="p === '...'" class="pagination-dots">...</span>
          <button v-else :class="['btn', p === page ? 'btn-primary' : 'btn-outline']" @click="goTo(p)">{{ p }}</button>
        </span>
        <button :disabled="page >= totalPages" @click="goTo(page + 1)" class="btn btn-outline">Selanjutnya</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getAdminBookings, updateBookingStatus } from '../../api'

const bookings = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')
const page = ref(1)
const totalPages = ref(1)

const visiblePages = computed(() => {
  const pages = []
  const current = page.value
  const last = totalPages.value
  if (last <= 5) {
    for (let i = 1; i <= last; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) pages.push(i)
    if (current < last - 2) pages.push('...')
    pages.push(last)
  }
  return pages
})

function getParams() {
  const params = { page: page.value }
  if (statusFilter.value) params.status = statusFilter.value
  if (search.value) params.search = search.value
  return params
}

async function fetchBookings() {
  loading.value = true
  try {
    const params = getParams()
    const res = await getAdminBookings(params)
    bookings.value = res.data.items
    totalPages.value = res.data.pagination.total_pages
  } catch (e) {
    alert(e.message || 'Gagal memuat data')
  } finally {
    loading.value = false
  }
}

function goTo(p) {
  page.value = p
  fetchBookings()
}

let debounceTimer
function onSearchInput() {
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
  } catch (e) {
    alert(e.message || 'Gagal update status')
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
