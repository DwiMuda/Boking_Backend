<template>
  <div class="container">
    <div class="section-header">
      <h1>Kamar &amp; Villa</h1>
      <p class="text-muted">Pilih tipe kamar yang sesuai untuk liburan Anda</p>
    </div>

    <AppSkeleton v-if="loading" type="card" :count="4" />

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="filteredRooms.length === 0">
      <AppEmptyState type="search" message="Kamar tidak ditemukan" />
    </div>

    <div v-else class="room-grid">
      <div v-for="(r, i) in filteredRooms" :key="r.id" class="room-card" v-observe :style="{ transitionDelay: `${i * 0.08}s` }" @click="$router.push(`/rooms/${r.id}`)">
        <div class="room-img">
          <div class="room-placeholder" :style="{ backgroundImage: 'url(' + getPhoto(i) + ')' }">
            <div class="room-placeholder-overlay"></div>
          </div>
          <div class="room-capacity">
            <UsersIcon :size="14" /> {{ r.kapasitas }} tamu
          </div>
        </div>
        <div class="room-body">
          <h3>{{ r.nama }}</h3>
          <p class="room-desc">{{ r.deskripsi }}</p>
          <div v-if="r.fasilitas" class="room-fasilitas">
            {{ r.fasilitas }}
          </div>
          <div class="room-footer">
            <div>
              <div class="room-price">Rp {{ formatHarga(r.harga_per_malam) }} <span class="text-muted fs-0-85">/malam</span></div>
            </div>
            <button class="btn btn-primary btn-sm" @click.stop="$router.push(`/rooms/${r.id}`)">Lihat</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getRooms } from '../api'
import { BedDoubleIcon, UsersIcon } from '@lucide/vue'

const route = useRoute()
const rooms = ref([])
const loading = ref(true)
const error = ref('')

const filteredRooms = computed(() => {
  const type = route.query.type?.toLowerCase()
  if (!type) return rooms.value
  return rooms.value.filter(r => r.nama?.toLowerCase().includes(type))
})
const colors = ['#1B3A2F', '#2D5A45', '#3D7A5C', '#4E9A73', '#5C8A6B']
const roomPhotos = [
  '/images/room-deluxe.jpg',
  '/images/villa-pool.jpg',
  '/images/room-family.jpg',
  '/images/suite-living.jpg',
  '/images/bedroom-canopy.jpg',
  '/images/pool-aerial.jpg',
]

onMounted(async () => {
  try {
    const res = await getRooms()
    rooms.value = res.data
  } catch (e) {
    error.value = e.message || 'Gagal memuat kamar'
  } finally {
    loading.value = false
  }
})

function getPhoto(i) { return roomPhotos[i % roomPhotos.length]; }
function formatHarga(h) { return new Intl.NumberFormat('id-ID').format(h); }
</script>

<style scoped>
.section-header { margin-bottom: 1.5rem; }
.section-header h1 { margin-bottom: 0.35rem; }
.section-header p { font-size: var(--text-sm); }

.room-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.room-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
  transition: all var(--duration-normal) var(--ease-smooth);
}

.room-card:hover {
  transform: translateY(-6px);
  border-color: var(--accent-primary-muted);
  box-shadow: var(--shadow-glow-primary);
}

.room-img { position: relative; }

.room-placeholder {
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-size: cover;
  background-position: center;
  transition: transform var(--duration-slow) var(--ease-smooth);
  position: relative;
}

.room-placeholder-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.4), transparent 60%);
}

.room-card:hover .room-placeholder { transform: scale(1.04); }

.room-capacity {
  position: absolute;
  top: 0.75rem;
  right: 0.75rem;
  background: rgba(0,0,0,0.5);
  color: white;
  padding: 0.3rem 0.7rem;
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  display: flex;
  align-items: center;
  gap: 0.35rem;
  backdrop-filter: blur(4px);
}

.room-body { padding: 1.25rem; }
.room-body h3 { font-size: var(--text-lg); margin-bottom: 0.35rem; }
.room-desc { color: var(--text-muted); font-size: var(--text-sm); line-height: var(--leading-snug); margin-bottom: 0.75rem; }
.room-fasilitas { color: var(--text-caption); font-size: var(--text-xs); margin-bottom: 0.75rem; line-height: var(--leading-relaxed); }
.room-footer { display: flex; align-items: center; justify-content: space-between; }
.room-price { font-family: var(--font-heading); font-weight: var(--weight-bold); color: var(--accent-warm); font-size: var(--text-base); }
</style>
