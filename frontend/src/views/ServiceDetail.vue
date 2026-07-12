<template>
  <div>
    <div v-if="loading" class="loading">Memuat layanan...</div>
    <div v-else-if="error" class="alert alert-error">{{ error }}</div>
    <div v-else-if="service" class="card max-w-600">
      <h2>{{ service.nama }}</h2>
      <p class="my-1 text-muted">{{ service.deskripsi }}</p>
      <div class="price">Rp {{ formatHarga(service.harga) }}</div>
      <div class="duration mb-1">Durasi: {{ service.durasi_menit }} menit</div>
      <routerLink :to="`/booking/${service.id}`" class="btn btn-primary">
        Booking Sekarang
      </routerLink>
      <routerLink to="/services" class="btn btn-outline ml-0-3">
        Kembali
      </routerLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getServiceDetail } from '../api'

const route = useRoute()
const service = ref(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const res = await getServiceDetail(route.params.id)
    service.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat detail layanan'
  } finally {
    loading.value = false
  }
})

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>
