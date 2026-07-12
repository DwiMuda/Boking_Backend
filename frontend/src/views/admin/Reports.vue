<template>
  <div class="container">
    <h2 class="mb-1">Laporan</h2>

    <div class="filter-bar">
      <input v-model="from" type="date" />
      <input v-model="to" type="date" />
      <button class="btn btn-primary btn-sm" @click="fetchReports">Cari</button>
      <button class="btn btn-secondary btn-sm flex items-center gap-0-5" @click="downloadPDF">
        <DownloadIcon :size="16" /> Download PDF
      </button>
    </div>

    <AppSkeleton v-if="loading" type="text" :count="4" />

    <div v-else-if="report">
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total Booking</span>
            <div class="stat-icon" style="background:var(--accent-primary-muted);color:var(--accent-primary)">
              <CalendarCheckIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ report.summary.total }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total Pendapatan</span>
            <div class="stat-icon" style="background:var(--semantic-success-bg);color:var(--semantic-success)">
              <WalletIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">Rp {{ formatHarga(report.summary.revenue) }}</div>
        </div>
      </div>

      <div class="card mb-1">
        <h3 class="mb-1">Grafik Pendapatan</h3>
        <div v-if="report.daily && report.daily.length > 0">
          <apexchart type="area" height="300" :options="chartOptions" :series="chartSeries"></apexchart>
        </div>
        <div v-else class="empty-state">
          <AppEmptyState type="default" message="Belum ada data untuk grafik" />
        </div>
      </div>

      <div class="card mb-1">
        <h3 class="mb-1">Per Layanan</h3>
        <div v-if="report.by_service.length > 0" class="table-wrapper">
          <table class="table">
            <thead>
              <tr><th>Layanan</th><th>Booking</th><th>Pendapatan</th></tr>
            </thead>
            <tbody>
              <tr v-for="s in report.by_service" :key="s.nama">
                <td>{{ s.nama }}</td><td>{{ s.total }}</td><td>Rp {{ formatHarga(s.revenue) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="empty-state">
          <AppEmptyState type="default" message="Belum ada data untuk periode ini" />
        </div>
      </div>

      <div class="card">
        <h3 class="mb-1">Per Hari</h3>
        <div v-if="report.daily.length > 0" class="table-wrapper">
          <table class="table">
            <thead>
              <tr><th>Tanggal</th><th>Layanan</th><th>Booking</th><th>Pendapatan</th></tr>
            </thead>
            <tbody>
              <tr v-for="d in report.daily" :key="d.tanggal + d.service_nama">
                <td>{{ d.tanggal }}</td><td>{{ d.service_nama }}</td><td>{{ d.total_booking }}</td><td>Rp {{ formatHarga(d.total_pendapatan) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="empty-state">
          <AppEmptyState type="default" message="Belum ada data harian" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, getCurrentInstance } from 'vue'
import { getAdminReports } from '../../api'
import { CalendarCheckIcon, WalletIcon, DownloadIcon } from '@lucide/vue'

const { proxy } = getCurrentInstance()

const from = ref('')
const to = ref('')
const report = ref(null)
const loading = ref(false)

function downloadPDF() {
  const token = localStorage.getItem('token') || ''
  const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  const url = `${apiBase}/api/admin/reports_pdf.php?from=${from.value}&to=${to.value}&token=${token}`
  window.open(url, '_blank')
}

async function fetchReports() {
  loading.value = true
  try {
    const res = await getAdminReports({ from: from.value, to: to.value })
    report.value = res.data
  } catch (e) {
    proxy.$toast(e.message || 'Gagal memuat laporan', 'error')
  } finally {
    loading.value = false
  }
}

fetchReports()

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}

const chartOptions = computed(() => {
  const dates = report.value?.daily ? [...new Set(report.value.daily.map(d => d.tanggal))] : []
  return {
    chart: { type: 'area', fontFamily: 'inherit', toolbar: { show: false }, parentHeightOffset: 0 },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: { categories: dates, labels: { style: { colors: 'var(--text-muted)' } } },
    yaxis: { labels: { formatter: (val) => 'Rp ' + (val / 1000) + 'k', style: { colors: 'var(--text-muted)' } } },
    colors: ['#cfa870'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
    grid: { borderColor: 'var(--border-subtle)', strokeDashArray: 4 }
  }
})

const chartSeries = computed(() => {
  if (!report.value?.daily) return []
  const byDate = {}
  report.value.daily.forEach(d => { byDate[d.tanggal] = (byDate[d.tanggal] || 0) + d.total_pendapatan })
  return [{ name: 'Pendapatan', data: Object.values(byDate) }]
})
</script>
