<template>
  <div class="gallery-page">
    <section class="gallery-hero section-dark">
      <div class="container text-center">
        <h1>Galeri <span class="text-gold">Resort</span></h1>
        <p>Momen dan sudut terbaik Tropical Resort</p>
      </div>
    </section>

    <section class="gallery-content">
      <div class="container">
        <div class="filter-bar justify-center">
          <button v-for="cat in categories" :key="cat" class="btn" :class="activeCat === cat ? 'btn-primary' : 'btn-ghost'" @click="activeCat = cat">{{ cat }}</button>
        </div>

        <div class="masonry-grid">
          <div v-for="(img, i) in filteredImages" :key="i" class="masonry-item" @click="openLightbox(i)">
            <div class="gallery-thumb" :style="{ backgroundImage: 'url(' + img.img + ')' }">
              <div class="thumb-overlay-blur"></div>
              <span class="thumb-label">{{ img.label }}</span>
              <div class="thumb-overlay">
                <SearchIcon :size="24" />
              </div>
            </div>
          </div>
        </div>

        <AppModal :open="lightboxOpen" @close="lightboxOpen = false">
          <div class="lightbox-content">
            <div class="lightbox-image" :style="{ backgroundImage: 'url(' + currentImage?.img + ')' }">
              <div class="lightbox-overlay-blur"></div>
              <span class="lightbox-label">{{ currentImage?.label }}</span>
            </div>
            <div class="lightbox-nav">
              <button class="btn btn-ghost" @click="prevImage">Sebelumnya</button>
              <span class="text-muted">{{ lightboxIndex + 1 }} / {{ filteredImages.length }}</span>
              <button class="btn btn-ghost" @click="nextImage">Selanjutnya</button>
            </div>
          </div>
        </AppModal>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { SearchIcon } from '@lucide/vue'

const activeCat = ref('Semua')
const categories = ['Semua', 'Kamar', 'Spa', 'Restoran', 'Area Umum']
const lightboxOpen = ref(false)
const lightboxIndex = ref(0)

const images = [
  { label: 'Kolam Renang Infinity', cat: 'Area Umum', img: '/images/pool-hero.jpg' },
  { label: 'Suite Room - Living Room', cat: 'Kamar', img: '/images/suite-living.jpg' },
  { label: 'Restoran Utama', cat: 'Restoran', img: '/images/restaurant.jpg' },
  { label: 'Ruang Spa', cat: 'Spa', img: '/images/spa.jpg' },
  { label: 'Deluxe Room - View', cat: 'Kamar', img: '/images/room-deluxe.jpg' },
  { label: 'Sunset dari Taman', cat: 'Area Umum', img: '/images/sunset-beach.jpg' },
  { label: 'Villa - Kolam Pribadi', cat: 'Kamar', img: '/images/villa-pool.jpg' },
  { label: 'Gym & Fitness', cat: 'Area Umum', img: '/images/gym.jpg' },
  { label: 'Private Dinner Setup', cat: 'Restoran', img: '/images/beach-dinner.jpg' },
]

const filteredImages = computed(() =>
  activeCat.value === 'Semua' ? images : images.filter(i => i.cat === activeCat.value)
)

const currentImage = computed(() => filteredImages.value[lightboxIndex.value])

function openLightbox(i) {
  lightboxIndex.value = i
  lightboxOpen.value = true
}

function nextImage() {
  lightboxIndex.value = (lightboxIndex.value + 1) % filteredImages.value.length
}

function prevImage() {
  lightboxIndex.value = (lightboxIndex.value - 1 + filteredImages.value.length) % filteredImages.value.length
}
</script>

<style scoped>
.gallery-hero { padding: 6rem 2rem 4rem; }
.gallery-hero h1 { font-size: var(--text-4xl); color: var(--text-on-dark); margin-bottom: 0.75rem; }
.gallery-hero p { color: rgba(255,255,255,0.6); }

.gallery-content { padding: 3rem 0 5rem; }
.masonry-grid { columns: 3; column-gap: 1rem; }
.masonry-item {
  break-inside: avoid;
  margin-bottom: 1rem;
  border-radius: var(--radius-md);
  overflow: hidden;
  cursor: pointer;
}

.gallery-thumb {
  position: relative;
  aspect-ratio: 4/3;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-family: var(--font-heading);
  font-size: var(--text-sm);
  font-weight: var(--weight-semibold);
  background-size: cover;
  background-position: center;
  transition: transform var(--duration-slow) var(--ease-smooth);
}

.thumb-overlay-blur {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 50%);
}

.thumb-label {
  position: relative;
  z-index: 1;
  align-self: flex-end;
  padding: 0.75rem;
  text-shadow: 0 1px 4px rgba(0,0,0,0.5);
}

.masonry-item:hover .gallery-thumb { transform: scale(1.04); }

.thumb-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity var(--duration-fast) var(--ease-smooth);
}

.masonry-item:hover .thumb-overlay { opacity: 1; }

.lightbox-content { padding: 1rem; }
.lightbox-image {
  aspect-ratio: 16/9;
  border-radius: var(--radius-md);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  color: white;
  font-family: var(--font-heading);
  font-size: var(--text-xl);
  font-weight: var(--weight-bold);
  background-size: cover;
  background-position: center;
  margin-bottom: 1rem;
  position: relative;
  overflow: hidden;
}

.lightbox-overlay-blur {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 40%);
}

.lightbox-label {
  position: relative;
  z-index: 1;
  padding: 1.5rem;
  text-shadow: 0 2px 8px rgba(0,0,0,0.5);
}

.lightbox-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

@media (max-width: 768px) {
  .masonry-grid { columns: 2; }
}
@media (max-width: 480px) {
  .masonry-grid { columns: 1; }
}
</style>
