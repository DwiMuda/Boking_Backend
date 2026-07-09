<template>
  <div>
    <h2 style="margin-bottom:1rem">Dashboard Admin</h2>
    <div v-if="loading" class="loading">Memuat data...</div>
    <div v-else-if="stats">
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_bookings }}</div>
          <div class="stat-label">Total Booking</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.pending_bookings }}</div>
          <div class="stat-label">Menunggu</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.confirmed_bookings }}</div>
          <div class="stat-label">Dikonfirmasi</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.completed_bookings }}</div>
          <div class="stat-label">Selesai</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_users }}</div>
          <div class="stat-label">Total User</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ stats.total_services }}</div>
          <div class="stat-label">Layanan Aktif</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">Rp {{ formatHarga(stats.total_revenue) }}</div>
          <div class="stat-label">Total Pendapatan</div>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-bottom:1rem">Booking 7 Hari Kedepan</h3>
        <table v-if="stats.weekly_bookings.length > 0" class="table">
          <tr><th>Tanggal</th><th>Jumlah Booking</th></tr>
          <tr v-for="wb in stats.weekly_bookings" :key="wb.tgl">
            <td>{{ wb.tgl }}</td><td>{{ wb.total }}</td>
          </tr>
        </table>
        <div v-else class="empty-state">Belum ada booking minggu ini</div>
      </div>

      <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
        <routerLink to="/admin/bookings" class="btn btn-primary">Kelola Booking</routerLink>
        <routerLink to="/admin/reports" class="btn btn-outline">Lihat Laporan</routerLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAdminDashboard } from '../../api'

const stats = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await getAdminDashboard()
    stats.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
