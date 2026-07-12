<template>
  <div>
    <AppSkeleton v-if="loading" type="card" />

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="room" class="room-detail">
      <div class="room-hero" :style="{ backgroundImage: 'url(' + getPhoto(room.id) + ')' }">
        <div class="room-hero-overlay-blur"></div>
        <div class="room-hero-content">
          <h1>{{ room.nama }}</h1>
          <div class="room-hero-meta">
            <span><UsersIcon :size="16" /> {{ room.kapasitas }} tamu</span>
            <span class="room-hero-price">Rp {{ formatHarga(room.harga_per_malam) }} /malam</span>
          </div>
        </div>
      </div>

      <div class="room-content">
        <div class="room-section">
          <h3>Deskripsi</h3>
          <p>{{ room.deskripsi }}</p>
        </div>

        <div class="room-section">
          <h3>Fasilitas</h3>
          <div class="fasilitas-list">
            <div v-for="f in fasilitasArray" :key="f" class="fasilitas-item">
              <CheckIcon :size="16" /> {{ f }}
            </div>
          </div>
        </div>

        <router-link :to="`/booking-room/${room.id}`" class="btn btn-gold btn-lg btn-full mt-1">
          Booking Kamar Ini
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getRoomDetail } from '../api'
import { BedDoubleIcon, UsersIcon, CheckIcon } from '@lucide/vue'

const route = useRoute()
const room = ref(null)
const loading = ref(true)
const error = ref('')

const roomPhotos = [
  '/images/room-deluxe.jpg',
  '/images/villa-pool.jpg',
  '/images/room-family.jpg',
  '/images/suite-living.jpg',
  '/images/bedroom-canopy.jpg',
  '/images/pool-aerial.jpg',
]

const fasilitasArray = computed(() =>
  room.value?.fasilitas ? room.value.fasilitas.split(',').map(f => f.trim()) : []
)

onMounted(async () => {
  try {
    const res = await getRoomDetail(route.params.id)
    room.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat detail kamar'
  } finally {
    loading.value = false
  }
})

function getPhoto(id) { return roomPhotos[id % roomPhotos.length]; }
function formatHarga(h) { return new Intl.NumberFormat('id-ID').format(h); }
</script>

<style scoped>
.room-detail { max-width: 700px; margin: 0 auto; padding: 0 1rem; }

.room-hero {
  position: relative;
  border-radius: var(--radius-lg);
  overflow: hidden;
  margin-bottom: 1.5rem;
  height: 280px;
  display: flex;
  align-items: flex-end;
  background-size: cover;
  background-position: center;
}

.room-hero-overlay-blur {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 50%);
}

.room-hero-content {
  position: relative;
  z-index: 1;
  padding: 1.5rem;
  width: 100%;
}

.room-hero-content h1 { font-size: var(--text-2xl); margin-bottom: 0.5rem; color: white; text-shadow: 0 2px 8px rgba(0,0,0,0.5); }

.room-hero-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  font-size: var(--text-sm);
  color: var(--text-muted);
}

.room-hero-meta svg { color: var(--accent-primary); }
.room-hero-price { font-family: var(--font-heading); font-weight: var(--weight-bold); color: var(--accent-warm); font-size: var(--text-lg); }

.room-content { }

.room-section { margin-bottom: 1.5rem; }
.room-section h3 { font-size: var(--text-lg); margin-bottom: 0.75rem; }
.room-section p { color: var(--text-muted); line-height: var(--leading-relaxed); }

.fasilitas-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.5rem; }

.fasilitas-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-sm);
  font-size: var(--text-sm);
  color: var(--text-secondary);
}

.fasilitas-item svg { color: var(--semantic-success); flex-shrink: 0; }
</style>
