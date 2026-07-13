<template>
  <div class="max-w-600">
    <div class="flex items-center gap-3 mb-1">
      <button class="btn btn-ghost btn-sm" @click="$router.push(`/services/${service?.id || ''}`)">
        <ArrowLeftIcon :size="18" />
        Kembali ke Detail
      </button>
      <h2 class="mb-0">Form Booking</h2>
    </div>

    <div v-if="error" class="alert alert-error">{{ error }}</div>

    <div v-if="service" class="card mb-1">
      <div class="flex justify-between items-center">
        <div>
          <h3>{{ service.nama }}</h3>
          <div class="duration mt-0-5">{{ service.durasi_menit }} menit</div>
        </div>
        <div class="price">Rp {{ formatHarga(service.harga) }}</div>
      </div>
    </div>

    <div class="card">
      <div class="form-group">
        <label>Pilih Tanggal</label>
        <input v-model="tgl" type="date" :min="minDate" @change="checkSlotAvailability" />
      </div>

      <div v-if="loadingSlots" class="loading">
        <AppSpinner variant="dots" size="sm" />
        <span>Memeriksa ketersediaan...</span>
      </div>

      <div v-else-if="slots.length > 0" class="form-group">
        <label>Pilih Jam</label>
        <div class="slot-grid">
          <button v-for="slot in slots" :key="slot" class="slot-btn" :class="{ selected: jam === slot }" :disabled="jam === slot" @click="jam = slot">
            {{ slot.substring(0, 5) }}
          </button>
        </div>
        <p v-if="jam" class="text-muted fs-0-9 mt-0-5">Jam dipilih: {{ jam.substring(0, 5) }}</p>
      </div>

      <div v-else-if="tgl && !loadingSlots" class="empty-state">
        <AppEmptyState type="search" message="Tidak ada jam tersedia untuk tanggal ini" />
      </div>

      <div class="form-group">
        <label>Catatan (opsional)</label>
        <textarea v-model="catatan" rows="3" placeholder="Catatan untuk booking..."></textarea>
      </div>

      <div class="flex gap-1">
        <button class="btn btn-primary flex-1" :disabled="!jam || submitting" @click="submitBooking">
          <AppSpinner v-if="submitting" variant="ring" size="sm" />
          {{ submitting ? 'Memproses...' : 'Konfirmasi Booking' }}
        </button>
        <button class="btn btn-ghost" @click="$router.push(`/services/${service?.id || ''}`)">Batal</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getServiceDetail, checkAvailability, createBooking } from '../api'
import { ArrowLeftIcon } from '@lucide/vue'

const route = useRoute()
const router = useRouter()
const service = ref(null)
const tgl = ref('')
const jam = ref('')
const catatan = ref('')
const slots = ref([])
const loadingSlots = ref(false)
const submitting = ref(false)
const error = ref('')

const minDate = computed(() => {
  const d = new Date()
  return d.toISOString().split('T')[0]
})

onMounted(async () => {
  try {
    const res = await getServiceDetail(route.params.serviceId)
    service.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat layanan'
  }
})

async function checkSlotAvailability() {
  if (!tgl.value || !service.value) return
  jam.value = ''
  slots.value = []
  loadingSlots.value = true
  error.value = ''
  try {
    const res = await checkAvailability(service.value.id, tgl.value)
    slots.value = res.data.available_slots
  } catch (e) {
    error.value = e.message
  } finally {
    loadingSlots.value = false
  }
}

async function submitBooking() {
  if (!jam.value) return
  submitting.value = true
  error.value = ''
  try {
    const res = await createBooking({
      service_id: service.value.id,
      tgl_booking: tgl.value,
      jam_booking: jam.value,
      catatan: catatan.value
    })
    router.push(`/booking/${res.data.id}/confirmation`)
  } catch (e) {
    error.value = e.message || 'Gagal membuat booking'
  } finally {
    submitting.value = false
  }
}

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>

<style scoped>
.flex-1 { flex: 1; }
.mb-0 { margin-bottom: 0; }
</style>