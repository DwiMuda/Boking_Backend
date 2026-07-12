<template>
  <div>
    <h2 class="mb-1">Booking Saya</h2>
    <div v-if="loading" class="loading">Memuat booking...</div>
    <div v-else-if="bookings.length === 0" class="empty-state">
      Belum ada booking. <router-link to="/services">Booking sekarang</router-link>
    </div>
    <div v-else>
      <div v-for="b in bookings" :key="b.id" class="card flex justify-between items-center">
        <div>
          <strong>{{ b.service_nama }}</strong>
          <div class="text-muted fs-0-9 mt-0-5">
            {{ b.tgl_booking }} {{ b.jam_booking.substring(0,5) }}
          </div>
          <div class="price fs-0-9 mt-0-5">Rp {{ formatHarga(b.total_harga) }}</div>
        </div>
        <div class="text-right">
          <span class="badge" :class="'badge-' + b.status">
            {{ statusLabel(b.status) }}
          </span>
          <div class="mt-0-5">
            <router-link :to="`/booking/${b.id}/confirmation`" class="btn btn-outline fs-0-85 p-0-4-0-8">
              Detail
            </router-link>
            <button v-if="b.status === 'pending'" class="btn btn-danger fs-0-85 p-0-4-0-8 ml-0-3" @click="handleCancel(b.id)">
              Batalkan
            </button>
          </div>
        </div>
      </div>
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
import { getMyBookings, cancelBooking } from '../api'

const bookings = ref([])
const loading = ref(true)
const page = ref(1)
const total = ref(0)
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

async function fetchBookings() {
  loading.value = true
  try {
    const res = await getMyBookings(page.value)
    bookings.value = res.data.items
    total.value = res.data.pagination.total
    totalPages.value = res.data.pagination.total_pages
  } catch (e) {
    alert(e.message || 'Gagal memuat data booking')
  } finally {
    loading.value = false
  }
}

function goTo(p) {
  page.value = p
  fetchBookings()
}

onMounted(fetchBookings)

async function handleCancel(id) {
  if (!confirm('Yakin ingin membatalkan booking ini?')) return
  try {
    await cancelBooking(id)
    bookings.value = bookings.value.map(b => b.id === id ? { ...b, status: 'cancelled' } : b)
  } catch (e) {
    alert(e.message || 'Gagal membatalkan booking')
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
