<template>
  <div>
    <h1 class="mb-1-5">Layanan Kami</h1>
    <div v-if="loading" class="loading">Memuat layanan...</div>
    <div v-else-if="error" class="alert alert-error">{{ error }}</div>
    <div v-else class="card-grid">
      <div v-for="s in services" :key="s.id" class="card card-service" @click="$router.push(`/services/${s.id}`)">
        <h3>{{ s.nama }}</h3>
        <p class="text-muted my-1 fs-0-9">{{ s.deskripsi }}</p>
        <div class="price">Rp {{ formatHarga(s.harga) }}</div>
        <div class="duration">{{ s.durasi_menit }} menit</div>
      </div>
    </div>
    <div v-if="services.length === 0 && !loading" class="empty-state">
      Belum ada layanan tersedia
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getServices } from '../api'

const services = ref([])
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const res = await getServices()
    services.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat layanan'
  } finally {
    loading.value = false
  }
})

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
