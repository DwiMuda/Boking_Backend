<template>
  <div class="max-w-600">
    <h2 class="mb-1">Konfirmasi Booking</h2>
    <div v-if="loading" class="loading">Memuat detail booking...</div>
    <div v-else-if="error" class="alert alert-error">{{ error }}</div>
    <div v-else-if="booking" class="card">
      <div class="alert alert-success">Booking berhasil dibuat!</div>
      <table class="table">
        <tr><th>ID Booking</th><td>#{{ booking.id }}</td></tr>
        <tr><th>Layanan</th><td>{{ booking.service_nama }}</td></tr>
        <tr><th>Tanggal</th><td>{{ booking.tgl_booking }}</td></tr>
        <tr><th>Jam</th><td>{{ booking.jam_booking.substring(0,5) }}</td></tr>
        <tr><th>Total Harga</th><td class="price">Rp {{ formatHarga(booking.total_harga) }}</td></tr>
        <tr><th>Status</th><td><span class="badge badge-pending">Menunggu Konfirmasi</span></td></tr>
      </table>
      <div class="flex gap-0-5 mt-1-5">
        <routerLink to="/my-bookings" class="btn btn-primary">Lihat Booking Saya</routerLink>
        <routerLink to="/services" class="btn btn-outline">Kembali ke Layanan</routerLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getBookingDetail } from '../api'

const route = useRoute()
const booking = ref(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const res = await getBookingDetail(route.params.id)
    booking.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat detail booking'
  } finally {
    loading.value = false
  }
})

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
