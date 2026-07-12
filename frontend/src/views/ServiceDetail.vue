<template>
  <div>
    <AppSkeleton v-if="loading" type="card" />

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="service" class="detail-page">
      <div class="detail-hero">
        <div class="detail-image">
          <div class="detail-photo" :style="{ backgroundImage: 'url(' + getServicePhoto(service) + ')' }">
            <div class="detail-photo-overlay"></div>
          </div>
        </div>
        <div class="detail-overlay">
          <div class="detail-meta-top">
            <span class="badge" :class="service.kategori === 'spa' ? 'badge-active' : 'badge-gold'">{{ service.kategori === 'spa' ? 'Spa' : 'Dining' }}</span>
          </div>
          <h1>{{ service.nama }}</h1>
          <p>{{ service.deskripsi }}</p>
        </div>
      </div>

      <div class="detail-body">
        <div class="detail-info-grid">
          <div class="detail-info-item">
            <ClockIcon :size="18" />
            <div>
              <span class="info-label">Durasi</span>
              <span class="info-value">{{ service.durasi_menit }} menit</span>
            </div>
          </div>
          <div class="detail-info-item">
            <WalletIcon :size="18" />
            <div>
              <span class="info-label">Harga</span>
              <span class="info-value price">Rp {{ formatHarga(service.harga) }}</span>
            </div>
          </div>
          <div v-if="service.fasilitas" class="detail-info-item full-width">
            <ListChecksIcon :size="18" />
            <div>
              <span class="info-label">Termasuk</span>
              <span class="info-value">{{ service.fasilitas }}</span>
            </div>
          </div>
        </div>

        <router-link :to="`/booking/${service.id}`" class="btn btn-primary btn-lg btn-full mt-1-5">
          Booking Sekarang
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getServiceDetail } from '../api'
import { ClockIcon, WalletIcon, ListChecksIcon, LeafIcon, FlowerIcon, SparklesIcon, UtensilsIcon, WineIcon, CoffeeIcon } from '@lucide/vue'

const route = useRoute()
const service = ref(null)
const loading = ref(true)
const error = ref('')

const iconComponent = computed(() => {
  if (!service.value) return SparklesIcon
  const map = {
    'Classic Massage': LeafIcon, 'Aromatherapy': FlowerIcon, 'Body Scrub': SparklesIcon,
    'Facial Treatment': FlowerIcon, 'Fine Dining Set Menu': UtensilsIcon, 'Breakfast Buffet': CoffeeIcon,
    'Afternoon Tea': CoffeeIcon, 'Private Dinner': WineIcon,
  }
  return map[service.value.nama] || (service.value.kategori === 'spa' ? SparklesIcon : UtensilsIcon)
})

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

function getServicePhoto(s) {
  const map = {
    'Classic Massage': '/images/spa.jpg',
    'Aromatherapy': '/images/spa.jpg',
    'Body Scrub': '/images/spa.jpg',
    'Facial Treatment': '/images/spa.jpg',
    'Fine Dining Set Menu': '/images/restaurant.jpg',
    'Breakfast Buffet': '/images/restaurant.jpg',
    'Afternoon Tea': '/images/restaurant.jpg',
    'Private Dinner': '/images/beach-dinner.jpg',
  }
  return map[s.nama] || (s.kategori === 'spa' ? '/images/spa.jpg' : '/images/restaurant.jpg')
}
</script>

<style scoped>
.detail-page { max-width: 700px; margin: 0 auto; padding: 0 1rem; }

.detail-hero {
  position: relative;
  border-radius: var(--radius-lg);
  overflow: hidden;
  margin-bottom: 1.5rem;
}

.detail-image { position: relative; }

.detail-photo {
  height: 220px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.detail-photo-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.5), transparent 50%);
}

.detail-overlay {
  padding: 1.5rem;
}

.detail-meta-top { margin-bottom: 0.75rem; }

.detail-overlay h1 {
  font-size: var(--text-2xl);
  margin-bottom: 0.5rem;
}

.detail-overlay p {
  color: var(--text-muted);
  line-height: var(--leading-relaxed);
}

.detail-body { padding: 0; }

.detail-info-grid { display: grid; gap: 1rem; }

.detail-info-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-sm);
}

.detail-info-item svg { color: var(--accent-primary); flex-shrink: 0; }

.info-label { display: block; font-size: var(--text-xs); color: var(--text-muted); }
.info-value { display: block; font-size: var(--text-sm); font-weight: var(--weight-semibold); }
.detail-info-item.full-width { grid-column: 1 / -1; }
</style>
