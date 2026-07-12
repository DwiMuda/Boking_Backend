<template>
  <div class="max-w-600 mx-auto">
    <div v-if="loading" class="loading">
      <AppSpinner variant="ring" size="lg" />
      <span>Memuat detail booking...</span>
    </div>

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="booking" class="confirmation-card">
      <div class="confirmation-header">
        <div class="success-icon">
          <CheckCircle2Icon :size="48" />
        </div>
        <h2>Booking Berhasil!</h2>
        <p class="text-muted">Booking Anda telah tercatat dan menunggu konfirmasi.</p>
      </div>

      <div class="confirmation-details">
        <h3>Detail Booking</h3>
        <div class="detail-grid">
          <div class="detail-row">
            <span class="detail-label">ID Booking</span>
            <span class="detail-value">#{{ booking.id }}</span>
          </div>

          <template v-if="booking.tipe_booking === 'room'">
            <div class="detail-row">
              <span class="detail-label">Tipe Kamar</span>
              <span class="detail-value">{{ booking.room_type_nama }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Nomor Kamar</span>
              <span class="detail-value">{{ booking.nomor_kamar }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Check-in</span>
              <span class="detail-value">{{ booking.check_in }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Check-out</span>
              <span class="detail-value">{{ booking.check_out }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Tamu</span>
              <span class="detail-value">{{ booking.jumlah_tamu }} orang</span>
            </div>
          </template>

          <template v-else>
            <div class="detail-row">
              <span class="detail-label">Layanan</span>
              <span class="detail-value">{{ booking.service_nama }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Tanggal</span>
              <span class="detail-value">{{ booking.tgl_booking }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Jam</span>
              <span class="detail-value">{{ booking.jam_booking?.substring(0, 5) }}</span>
            </div>
          </template>

          <div class="detail-row">
            <span class="detail-label">Total Harga</span>
            <span class="detail-value price">Rp {{ formatHarga(booking.total_harga) }}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Status</span>
            <span class="badge badge-pending">Menunggu Konfirmasi</span>
          </div>
        </div>
      </div>

      <div class="confirmation-actions">
        <routerLink to="/my-bookings" class="btn btn-primary">Lihat Booking Saya</routerLink>
        <routerLink to="/services" class="btn btn-ghost">Kembali</routerLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getBookingDetail } from '../api'
import { CheckCircle2Icon } from '@lucide/vue'

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

<style scoped>
.confirmation-card { background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); overflow: hidden; }
.confirmation-header { text-align: center; padding: 2.5rem 2rem 1.5rem; border-bottom: 1px solid var(--border-subtle); }
.success-icon { color: var(--semantic-success); margin-bottom: 1rem; animation: successPop 0.5s var(--ease-bounce); }
@keyframes successPop { 0% { transform: scale(0); opacity: 0; } 60% { transform: scale(1.15); } 100% { transform: scale(1); opacity: 1; } }
.confirmation-header h2 { margin-bottom: 0.5rem; }
.confirmation-header p { font-size: var(--text-sm); }
.confirmation-details { padding: 1.5rem 2rem; }
.confirmation-details h3 { font-size: var(--text-base); margin-bottom: 1rem; color: var(--text-secondary); }
.detail-grid { display: flex; flex-direction: column; gap: 0.75rem; }
.detail-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--border-subtle); }
.detail-row:last-child { border-bottom: none; }
.detail-label { font-size: var(--text-sm); color: var(--text-muted); }
.detail-value { font-size: var(--text-sm); font-weight: var(--weight-medium); color: var(--text-primary); }
.confirmation-actions { display: flex; gap: 0.75rem; padding: 1.25rem 2rem 2rem; flex-wrap: wrap; }
</style>
