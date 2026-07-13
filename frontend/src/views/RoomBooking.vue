<template>
  <div class="max-w-600 mx-auto">
    <div class="flex items-center gap-3 mb-1">
      <button class="btn btn-ghost btn-sm" @click="$router.push(`/rooms/${roomType?.id || ''}`)">
        <ArrowLeftIcon :size="18" />
        Kembali ke Detail
      </button>
      <h2 class="mb-0">Booking Kamar</h2>
    </div>

    <AppSkeleton v-if="loading" />

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="roomType" class="card mb-1">
      <div class="flex justify-between items-center">
        <div>
          <h3>{{ roomType.nama }}</h3>
          <div class="text-muted fs-0-9 mt-0-5">Kapasitas {{ roomType.kapasitas }} tamu</div>
        </div>
        <div class="room-price">Rp {{ formatHarga(roomType.harga_per_malam) }}<span class="text-muted fs-0-85">/malam</span></div>
      </div>
    </div>

    <div class="card">
      <div class="form-row">
        <div class="form-group">
          <label>Check-in</label>
          <input v-model="checkIn" type="date" :min="tomorrow" @change="calcTotal" />
        </div>
        <div class="form-group">
          <label>Check-out</label>
          <input v-model="checkOut" type="date" :min="checkIn || tomorrow" @change="calcTotal" />
        </div>
      </div>

      <div class="form-group">
        <label>Jumlah Tamu</label>
        <input v-model.number="jumlahTamu" type="number" min="1" :max="roomType?.kapasitas || 10" @change="calcTotal" />
      </div>

      <div v-if="nights > 0" class="price-breakdown">
        <div class="pb-row">
          <span>Rp {{ formatHarga(roomType?.harga_per_malam) }} x {{ nights }} malam</span>
          <span>Rp {{ formatHarga(totalHarga) }}</span>
        </div>
        <div class="pb-total">
          <span>Total</span>
          <span class="price">Rp {{ formatHarga(totalHarga) }}</span>
        </div>
      </div>

      <div class="form-group">
        <label>Catatan (opsional)</label>
        <textarea v-model="catatan" rows="2" placeholder="Permintaan khusus..."></textarea>
      </div>

      <button class="btn btn-gold btn-full" :disabled="!canBook || submitting" @click="submitBooking">
        <AppSpinner v-if="submitting" variant="ring" size="sm" />
        {{ submitting ? 'Memproses...' : 'Konfirmasi Booking Kamar' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getRoomDetail, bookRoom } from '../api'
import { ArrowLeftIcon } from '@lucide/vue'

const route = useRoute()
const router = useRouter()
const roomType = ref(null)
const loading = ref(true)
const submitting = ref(false)
const error = ref('')
const checkIn = ref('')
const checkOut = ref('')
const jumlahTamu = ref(2)
const catatan = ref('')

const tomorrow = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const nights = computed(() => {
  if (!checkIn.value || !checkOut.value || checkOut.value <= checkIn.value) return 0
  return Math.ceil((new Date(checkOut.value) - new Date(checkIn.value)) / 86400000)
})

const totalHarga = computed(() => {
  if (!roomType.value || nights.value === 0) return 0
  return roomType.value.harga_per_malam * nights.value
})

const canBook = computed(() => checkIn.value && checkOut.value && nights.value > 0)

onMounted(async () => {
  try {
    const res = await getRoomDetail(route.params.roomTypeId)
    roomType.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat data kamar'
  } finally {
    loading.value = false
  }
})

function calcTotal() {}

async function submitBooking() {
  if (!canBook.value) return
  submitting.value = true
  error.value = ''
  try {
    const res = await bookRoom({
      room_type_id: roomType.value.id,
      check_in: checkIn.value,
      check_out: checkOut.value,
      jumlah_tamu: jumlahTamu.value,
      catatan: catatan.value
    })
    router.push(`/booking/${res.data.id}/confirmation`)
  } catch (e) {
    error.value = e.message || 'Gagal booking kamar'
  } finally {
    submitting.value = false
  }
}

function formatHarga(h) { return new Intl.NumberFormat('id-ID').format(h); }
</script>

<style scoped>
.room-price { font-family: var(--font-heading); font-weight: var(--weight-bold); color: var(--accent-warm); font-size: var(--text-lg); }

.price-breakdown {
  background: var(--bg-elevated);
  border-radius: var(--radius-sm);
  padding: 1rem;
  margin-bottom: 1.25rem;
}

.pb-row {
  display: flex;
  justify-content: space-between;
  font-size: var(--text-sm);
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.pb-total {
  display: flex;
  justify-content: space-between;
  padding-top: 0.75rem;
  border-top: 1px solid var(--border-subtle);
  font-family: var(--font-heading);
  font-weight: var(--weight-bold);
  font-size: var(--text-lg);
}

.mb-0 { margin-bottom: 0; }
</style>