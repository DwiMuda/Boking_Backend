<template>
  <div class="container">
    <div class="section-header">
      <h1>Layanan Resort</h1>
      <p class="text-muted">Pilih layanan Spa &amp; Dining untuk melengkapi liburan Anda</p>
    </div>

    <div class="category-filter">
      <button v-for="cat in categories" :key="cat.key" class="cat-btn" :class="{ active: activeCat === cat.key }" @click="activeCat = cat.key">
        <component :is="cat.icon" :size="18" />
        {{ cat.label }}
      </button>
      <router-link to="/rooms" class="cat-btn cat-room" :class="$route.path === '/rooms' ? 'active' : ''">
        <BedDoubleIcon :size="18" />
        Kamar
      </router-link>
    </div>

    <AppSkeleton v-if="loading" type="card" :count="4" />

    <div v-else-if="error" class="alert alert-error">{{ error }}</div>

    <div v-else-if="filteredServices.length === 0" class="empty-state">
      <AppEmptyState type="default" :message="`Belum ada layanan ${activeLabel}`" />
    </div>

    <div v-else class="card-grid">
      <div v-for="(s, i) in filteredServices" :key="s.id" class="service-card" :style="{ animationDelay: `${i * 0.06}s` }" @click="$router.push(`/services/${s.id}`)">
        <div class="service-img">
          <div class="service-photo" :style="{ backgroundImage: 'url(' + getServicePhoto(s) + ')' }">
            <div class="service-photo-overlay"></div>
          </div>
        </div>
        <div class="service-body">
          <span class="badge" :class="s.kategori === 'spa' ? 'badge-active' : 'badge-gold'">{{ s.kategori === 'spa' ? 'Spa' : 'Dining' }}</span>
          <h3>{{ s.nama }}</h3>
          <p class="service-desc">{{ s.deskripsi }}</p>
          <div class="service-footer">
            <div>
              <div class="price">Rp {{ formatHarga(s.harga) }}</div>
              <div class="duration">{{ s.durasi_menit }} menit</div>
            </div>
            <button class="btn btn-primary btn-sm" @click.stop="$router.push(`/services/${s.id}`)">
              Pesan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getServices } from '../api'
import { SparklesIcon, UtensilsIcon, ClockIcon, BedDoubleIcon, LeafIcon, WineIcon, FlowerIcon, CoffeeIcon } from '@lucide/vue'

const route = useRoute()
const services = ref([])
const loading = ref(true)
const error = ref('')
const activeCat = ref(route.query.kategori || 'spa')

const categories = [
  { key: 'spa', label: 'Spa & Wellness', icon: SparklesIcon },
  { key: 'dining', label: 'Restoran & Bar', icon: UtensilsIcon },
]

const activeLabel = computed(() => categories.find(c => c.key === activeCat.value)?.label || '')

const filteredServices = computed(() =>
  services.value.filter(s => s.kategori === activeCat.value)
)

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

function getIcon(s) {
  const map = {
    'Classic Massage': LeafIcon,
    'Aromatherapy': FlowerIcon,
    'Body Scrub': SparklesIcon,
    'Facial Treatment': FlowerIcon,
    'Fine Dining Set Menu': UtensilsIcon,
    'Breakfast Buffet': CoffeeIcon,
    'Afternoon Tea': CoffeeIcon,
    'Private Dinner': WineIcon,
  }
  return map[s.nama] || (s.kategori === 'spa' ? SparklesIcon : UtensilsIcon)
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

function formatHarga(harga) {
  return new Intl.NumberFormat('id-ID').format(harga)
}
</script>

<style scoped>
.section-header { margin-bottom: 1.5rem; }
.section-header h1 { margin-bottom: 0.35rem; }
.section-header p { font-size: var(--text-sm); }

.category-filter {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.cat-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.55rem 1.2rem;
  border-radius: var(--radius-full);
  border: 1px solid var(--border-default);
  background: var(--bg-surface);
  color: var(--text-muted);
  font-family: var(--font-heading);
  font-size: var(--text-sm);
  font-weight: var(--weight-medium);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-smooth);
}

.cat-btn:hover { border-color: var(--accent-primary); color: var(--accent-primary); }
.cat-btn.active { background: var(--accent-primary); color: white; border-color: var(--accent-primary); }
.cat-room.active { background: var(--accent-gold); color: white; border-color: var(--accent-gold); }

.service-card { animation: cardFadeIn 0.5s var(--ease-smooth) both; }

@keyframes cardFadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to { opacity: 1; transform: translateY(0); }
}

.badge { margin-bottom: 0.5rem; }

.service-photo {
  height: 160px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.service-photo-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.3), transparent 50%);
}
</style>
