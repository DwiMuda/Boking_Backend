<template>
  <div>
    <h2 style="margin-bottom:1rem">Booking Saya</h2>
    <div v-if="loading" class="loading">Memuat booking...</div>
    <div v-else-if="bookings.length === 0" class="empty-state">
      Belum ada booking. <routerLink to="/services">Booking sekarang</routerLink>
    </div>
    <div v-else>
      <div v-for="b in bookings" :key="b.id" class="card" style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <strong>{{ b.service_nama }}</strong>
          <div style="color:#666;font-size:0.9rem;margin-top:0.3rem">
            {{ b.tgl_booking }} {{ b.jam_booking.substring(0,5) }}
          </div>
          <div class="price" style="font-size:1rem;margin-top:0.3rem">Rp {{ formatHarga(b.total_harga) }}</div>
        </div>
        <div style="text-align:right">
          <span class="badge" :class="'badge-' + b.status">
            {{ statusLabel(b.status) }}
          </span>
          <div style="margin-top:0.5rem">
            <routerLink :to="`/booking/${b.id}/confirmation`" class="btn btn-outline" style="font-size:0.85rem;padding:0.4rem 0.8rem">
              Detail
            </routerLink>
            <button v-if="b.status === 'pending'" class="btn btn-danger" style="font-size:0.85rem;padding:0.4rem 0.8rem;margin-left:0.3rem" @click="handleCancel(b.id)">
              Batalkan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getMyBookings, cancelBooking } from '../api'

const bookings = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await getMyBookings()
    bookings.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

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
