<template>
  <div>
    <h2 style="margin-bottom:1rem">Kelola Booking</h2>

    <div class="filter-bar">
      <input v-model="search" placeholder="Cari user/layanan..." @input="fetchBookings" />
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
    <div v-else class="card" style="overflow-x:auto">
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
              <select v-if="b.status !== 'cancelled'" :value="b.status" @change="updateStatus(b.id, $event.target.value)" style="padding:0.3rem;border-radius:6px;border:1px solid #ddd">
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
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAdminBookings, updateBookingStatus } from '../../api'

const bookings = ref([])
const loading = ref(true)
const search = ref('')
const statusFilter = ref('')

onMounted(() => fetchBookings())

let debounceTimer
async function fetchBookings() {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(async () => {
    loading.value = true
    try {
      const params = {}
      if (statusFilter.value) params.status = statusFilter.value
      if (search.value) params.search = search.value
      const res = await getAdminBookings(params)
      bookings.value = res.data
    } catch (e) {
      console.error(e)
    } finally {
      loading.value = false
    }
  }, 300)
}

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
