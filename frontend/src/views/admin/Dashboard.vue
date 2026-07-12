<template>
  <div class="container">
    <h2 class="mb-1">Dashboard</h2>

    <AppSkeleton v-if="loading" type="card" :count="4" />

    <div v-else-if="stats">
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total Booking</span>
            <div class="stat-icon" style="background:var(--accent-primary-muted);color:var(--accent-primary)">
              <CalendarCheckIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.total_bookings }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Menunggu</span>
            <div class="stat-icon" style="background:var(--badge-pending-bg);color:var(--badge-pending)">
              <ClockIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.pending_bookings }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Dikonfirmasi</span>
            <div class="stat-icon" style="background:var(--badge-confirmed-bg);color:var(--badge-confirmed)">
              <CheckCircleIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.confirmed_bookings }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Selesai</span>
            <div class="stat-icon" style="background:var(--badge-completed-bg);color:var(--badge-completed)">
              <FlagIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.completed_bookings }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total User</span>
            <div class="stat-icon" style="background:var(--semantic-info-bg);color:var(--semantic-info)">
              <UsersIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.total_users }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Layanan Aktif</span>
            <div class="stat-icon" style="background:var(--accent-secondary-muted);color:var(--accent-secondary)">
              <WrenchIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.total_services }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Tipe Kamar</span>
            <div class="stat-icon" style="background:var(--accent-gold-muted);color:var(--accent-gold)">
              <BedDoubleIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.total_room_types }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Kamar Tersedia</span>
            <div class="stat-icon" style="background:var(--accent-secondary-muted);color:var(--accent-secondary)">
              <DoorOpenIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">{{ stats.total_rooms }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-label">Total Pendapatan</span>
            <div class="stat-icon" style="background:var(--semantic-warning-bg);color:var(--semantic-warning)">
              <WalletIcon :size="20" />
            </div>
          </div>
          <div class="stat-value">Rp {{ formatHarga(stats.total_revenue) }}</div>
        </div>
      </div>

      <div class="card mb-1">
        <h3 class="mb-1">Booking 7 Hari Kedepan</h3>
        <div v-if="stats.weekly_bookings.length > 0" class="table-wrapper">
          <table class="table">
            <thead>
              <tr><th>Tanggal</th><th>Jumlah Booking</th></tr>
            </thead>
            <tbody>
              <tr v-for="wb in stats.weekly_bookings" :key="wb.tgl">
                <td>{{ wb.tgl }}</td><td>{{ wb.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="empty-state">
          <AppEmptyState type="booking" message="Belum ada booking minggu ini" />
        </div>
      </div>

      <div class="flex gap-0-5 flex-wrap">
        <routerLink to="/admin/bookings" class="btn btn-primary">Kelola Booking</routerLink>
        <routerLink to="/admin/rooms" class="btn btn-gold">Kelola Kamar</routerLink>
        <routerLink to="/admin/reports" class="btn btn-ghost">Lihat Laporan</routerLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAdminDashboard } from '../../api'
import { CalendarCheckIcon, ClockIcon, CheckCircleIcon, FlagIcon, UsersIcon, WrenchIcon, BedDoubleIcon, DoorOpenIcon, WalletIcon } from '@lucide/vue'

const stats = ref(null)
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await getAdminDashboard()
    stats.value = res.data
  } catch (e) {
    alert(e.message || 'Gagal memuat data dashboard')
  } finally {
    loading.value = false
  }
})

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
