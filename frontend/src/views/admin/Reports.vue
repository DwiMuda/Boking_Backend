<template>
  <div>
    <h2 class="mb-1">Laporan</h2>

    <div class="filter-bar">
      <input v-model="from" type="date" />
      <input v-model="to" type="date" />
      <button class="btn btn-primary" @click="fetchReports">Cari</button>
    </div>

    <div v-if="loading" class="loading">Memuat laporan...</div>
    <div v-else-if="report">
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-value">{{ report.summary.total }}</div>
          <div class="stat-label">Total Booking</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">Rp {{ formatHarga(report.summary.revenue) }}</div>
          <div class="stat-label">Total Pendapatan</div>
        </div>
      </div>

      <div class="card">
        <h3 class="mb-1">Per Layanan</h3>
        <table v-if="report.by_service.length > 0" class="table">
          <thead>
            <tr><th>Layanan</th><th>Booking</th><th>Pendapatan</th></tr>
          </thead>
          <tbody>
            <tr v-for="s in report.by_service" :key="s.nama">
              <td>{{ s.nama }}</td><td>{{ s.total }}</td><td>Rp {{ formatHarga(s.revenue) }}</td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty-state">Belum ada data</div>
      </div>

      <div class="card">
        <h3 class="mb-1">Per Hari</h3>
        <table v-if="report.daily.length > 0" class="table">
          <thead>
            <tr><th>Tanggal</th><th>Layanan</th><th>Booking</th><th>Pendapatan</th></tr>
          </thead>
          <tbody>
            <tr v-for="d in report.daily" :key="d.tanggal + d.service_nama">
              <td>{{ d.tanggal }}</td><td>{{ d.service_nama }}</td><td>{{ d.total_booking }}</td><td>Rp {{ formatHarga(d.total_pendapatan) }}</td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty-state">Belum ada data</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { getAdminReports } from '../../api'

const from = ref(new Date(Date.now() - 30*86400000).toISOString().split('T')[0])
const to = ref(new Date().toISOString().split('T')[0])
const report = ref(null)
const loading = ref(false)

async function fetchReports() {
  loading.value = true
  try {
    const res = await getAdminReports({ from: from.value, to: to.value })
    report.value = res.data
  } catch (e) {
    alert(e.message || 'Gagal memuat laporan')
  } finally {
    loading.value = false
  }
}

fetchReports()

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
