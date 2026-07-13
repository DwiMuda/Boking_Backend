<template>
  <div class="home-luxury">
    <!-- HERO SECTION -->
    <section class="hero-luxury">
      <div class="hero-bg" style="background-image: url('/images/pool-hero.jpg');"></div>
      <div class="hero-overlay-luxury"></div>
      
      <div class="hero-content-luxury">
        <div class="hero-badge-luxury animate-fade-up">
          <SparklesIcon :size="14" /> <span>Resort & Spa · Bali</span>
        </div>
        <h1 class="hero-title-luxury animate-fade-up delay-100">
          Tenggelam dalam <br>
          <span class="text-gold-luxury">Kemewahan Tropis</span>
        </h1>
        <p class="hero-subtitle-luxury animate-fade-up delay-200">
          Sebuah pelarian sempurna dari hiruk-pikuk dunia. Rasakan layanan bintang lima, villa eksklusif, dan relaksasi tak terlupakan.
        </p>
      </div>

      <!-- SEARCH BAR WIDGET (LIGHT) -->
      <div class="search-widget-luxury animate-fade-up delay-400">
        <div class="sw-grid-luxury">
          <div class="sw-field-luxury">
            <label>Check-in</label>
            <input v-model="searchCheckIn" type="date" :min="tomorrow" />
          </div>
          <div class="sw-field-luxury">
            <label>Check-out</label>
            <input v-model="searchCheckOut" type="date" :min="searchCheckIn || tomorrow" />
          </div>
          <div class="sw-field-luxury">
            <label>Layanan</label>
            <select v-model="searchType">
              <option value="">Pilih Semua</option>
              <option value="room">Kamar & Villa</option>
              <option value="spa">Spa & Relaksasi</option>
              <option value="dining">Restoran</option>
            </select>
          </div>
          <div class="sw-action-luxury">
            <button class="btn-luxury-primary" @click="doSearch">
              <SearchIcon :size="18" /> TEMUKAN
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- FEATURES (KENAPA PILIH KAMI) -->
    <section class="features-luxury bg-sand">
      <div class="container">
        <div class="section-heading-luxury text-center">
          <span class="subheading">Pengalaman Kami</span>
          <h2>Harmoni Alam & <span class="text-gold-luxury">Eksklusivitas</span></h2>
          <p>Dirancang khusus untuk memanjakan setiap indra Anda</p>
        </div>
        <div class="features-grid-luxury">
          <div v-for="(f, i) in features" :key="i" class="feature-card-luxury">
            <div class="fc-icon-wrapper" :style="{ color: f.color, background: f.bg }">
              <component :is="f.icon" :size="28" stroke-width="1.5" />
            </div>
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ROOM PREVIEW -->
    <section class="rooms-preview-luxury bg-white">
      <div class="container">
        <div class="section-heading-luxury text-center">
          <span class="subheading">Akomodasi</span>
          <h2>Kamar & <span class="text-gold-luxury">Villa Premium</span></h2>
          <p>Desain elegan berpadu dengan pemandangan menakjubkan</p>
        </div>
        
        <div class="rp-grid-luxury">
          <div class="rp-card-luxury" v-for="(r, i) in roomPreviews" :key="i">
            <div class="rp-image-wrapper">
              <div class="rp-image" :style="{ backgroundImage: 'url(' + r.img + ')' }"></div>
              <div class="rp-badge">{{ r.badge }}</div>
            </div>
            <div class="rp-content">
              <h3>{{ r.name }}</h3>
              <p>{{ r.desc }}</p>
              <div class="rp-bottom">
                <div class="rp-price">
                  <span class="price-val">{{ r.price }}</span>
                  <span class="price-label">/ malam</span>
                </div>
                <router-link :to="'/rooms?type=' + r.slug" class="btn-luxury-outline">Lihat Detail</router-link>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center mt-3">
          <router-link to="/rooms" class="btn-luxury-secondary">Lihat Seluruh Akomodasi</router-link>
        </div>
      </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testi-luxury bg-sand">
      <div class="container">
        <div class="testi-wrapper">
          <div class="quote-icon">
            <QuoteIcon :size="48" stroke-width="1" />
          </div>
          <div class="testi-content">
            <transition name="fade" mode="out-in">
              <div :key="testiIndex" class="testi-active">
                <blockquote>"{{ testimonials[testiIndex].quote }}"</blockquote>
                <div class="testi-author">
                  <strong>{{ testimonials[testiIndex].name }}</strong>
                  <span>{{ testimonials[testiIndex].visit }}</span>
                </div>
              </div>
            </transition>
          </div>
          <div class="testi-nav">
            <button v-for="(t, i) in testimonials" :key="i" :class="{ active: testiIndex === i }" @click="testiIndex = i"></button>
          </div>
        </div>
      </div>
    </section>

    <!-- OFFERS -->
    <section class="offers-luxury bg-white">
      <div class="container">
        <div class="section-heading-luxury text-center">
          <span class="subheading">Penawaran Eksklusif</span>
          <h2>Momen Spesial <span class="text-gold-luxury">Anda</span></h2>
        </div>
        <div class="offers-grid-luxury">
          <div v-for="(o, i) in specialOffers" :key="i" class="offer-card-luxury">
            <div class="offer-card-inner">
              <div class="oc-badge">{{ o.badge }}</div>
              <h3>{{ o.title }}</h3>
              <p>{{ o.desc }}</p>
              <div class="oc-footer">
                <span class="oc-valid"><ClockIcon :size="14"/> {{ o.valid }}</span>
                <router-link to="/services" class="oc-link">Reservasi <ArrowRightIcon :size="16"/></router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer-luxury">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <div class="fb-logo">
              <TreePineIcon :size="24" stroke-width="1.5" /> Tropical Resort
            </div>
            <p>Destinasi kemewahan tropis yang memadukan keindahan alam dengan pelayanan elegan nan tak terlupakan.</p>
          </div>
          <div class="footer-links">
            <h4>Eksplorasi</h4>
            <router-link to="/rooms">Kamar & Villa</router-link>
            <router-link to="/services?kategori=spa">Spa & Wellness</router-link>
            <router-link to="/services?kategori=dining">Fine Dining</router-link>
            <router-link to="/gallery">Galeri Resort</router-link>
          </div>
          <div class="footer-contact">
            <h4>Hubungi Kami</h4>
            <p><MapPinIcon :size="14" /> Jl. Tropis Indah No. 1, Bali</p>
            <p><PhoneIcon :size="14" /> +62 812 3456 7890</p>
            <p><MailIcon :size="14" /> info@tropicalresort.com</p>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2026 Tropical Resort & Spa. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  SparklesIcon, SearchIcon, DropletsIcon, UtensilsCrossedIcon, LeafIcon, WavesIcon, 
  SunIcon, TreePineIcon, MapPinIcon, PhoneIcon, MailIcon, ClockIcon, QuoteIcon,
  ArrowRightIcon
} from '@lucide/vue'

const router = useRouter()

const tomorrow = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const searchCheckIn = ref('')
const searchCheckOut = ref('')
const searchType = ref('')
const testiIndex = ref(0)

function doSearch() {
  const params = {}
  if (searchType.value === 'room') {
    router.push({ path: '/rooms' })
    return
  }
  if (searchType.value) params.kategori = searchType.value
  router.push({ path: '/services', query: params })
}

const features = [
  { icon: DropletsIcon, title: 'Infinity Pool', desc: 'Kolam renang tanpa batas dengan panorama laut lepas, dilengkapi layanan butler pribadi.', color: '#059669', bg: '#ecfdf5' },
  { icon: SparklesIcon, title: 'Spa & Wellness', desc: 'Perawatan tubuh tradisional Bali dan terapi holistik di ruang spa privat kami.', color: '#d97706', bg: '#fffbeb' },
  { icon: UtensilsCrossedIcon, title: 'Fine Dining', desc: 'Mahakarya kuliner dari chef bintang Michelin dengan pemandangan matahari terbenam.', color: '#dc2626', bg: '#fef2f2' },
  { icon: LeafIcon, title: 'Taman Tropis', desc: 'Area hijau seluas 2 hektar dengan berbagai koleksi tanaman tropis eksotis yang asri.', color: '#16a34a', bg: '#f0fdf4' },
  { icon: SunIcon, title: 'Private Beach', desc: 'Pantai pribadi berpasir putih eksklusif khusus bagi para tamu resort.', color: '#ea580c', bg: '#fff7ed' },
  { icon: WavesIcon, title: 'Sunset Cruise', desc: 'Pelayaran sore melintasi teluk sambil menikmati koktail dan hidangan ringan.', color: '#7c3aed', bg: '#f5f3ff' },
]

const roomPreviews = [
  { name: 'Deluxe Ocean Suite', desc: 'Kamar luas dengan balkon pribadi menghadap langsung ke panorama Samudra Hindia.', price: 'Rp 2.500.000', badge: 'Terfavorit', slug: 'deluxe', img: '/images/room-deluxe.jpg' },
  { name: 'Premium Garden Villa', desc: 'Villa tersembunyi di tengah taman tropis dengan kolam renang pribadi eksklusif.', price: 'Rp 4.200.000', badge: 'Villa', slug: 'premium', img: '/images/villa-pool.jpg' },
  { name: 'Royal Family Suite', desc: 'Suite dua kamar megah dengan ruang keluarga luas, dirancang untuk kebersamaan elegan.', price: 'Rp 3.800.000', badge: 'Keluarga', slug: 'family', img: '/images/room-family.jpg' },
]

const testimonials = [
  { name: 'Sarah Wijaya', quote: 'Setiap detiknya terasa magis. Arsitektur, layanan butler, dan ketenangan yang ditawarkan sangat luar biasa. Ini bukan sekadar menginap, tapi pengalaman spiritual.', visit: 'Maret 2026' },
  { name: 'Budi Santoso', quote: 'Menu fine dining-nya adalah karya seni. Spa tradisional Bali benar-benar menghidupkan kembali energi saya setelah berbulan-bulan bekerja keras.', visit: 'April 2026' },
  { name: 'Maya Putri', quote: 'Sempurna untuk bulan madu. Privasi di Garden Villa sangat terjaga, dan staf sangat memperhatikan setiap detail kecil kebutuhan kami.', visit: 'Februari 2026' }
]

const specialOffers = [
  { title: 'Honeymoon Bliss', desc: 'Menginap 3 malam di Villa, gratis romantic dinner & couple spa.', badge: 'Eksklusif', valid: 'S.d 30 Sep 2026' },
  { title: 'Extended Paradise', desc: 'Nikmati malam ke-4 secara cuma-cuma untuk setiap pemesanan 3 malam.', badge: 'Promo Spesial', valid: 'S.d 31 Des 2026' },
  { title: 'Culinary Journey', desc: 'Diskon 35% untuk paket 5-course dinner dengan wine pairing kelas dunia.', badge: 'Terbatas', valid: 'S.d 15 Okt 2026' },
]

let testiTimer
onMounted(() => {
  testiTimer = setInterval(() => {
    testiIndex.value = (testiIndex.value + 1) % testimonials.length
  }, 6000)
})
onUnmounted(() => {
  clearInterval(testiTimer)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

/* GLOBAL LUXURY VARS */
.home-luxury {
  --font-serif: 'Playfair Display', serif;
  --font-sans: 'Inter', sans-serif;
  --color-gold: #C9A15C;
  --color-gold-hover: #b38d4a;
  --color-sand: #FAFAF8;
  --color-dark: #1C2026;
  --color-gray: #6B7280;
  
  font-family: var(--font-sans);
  color: var(--color-dark);
  overflow-x: hidden;
  background: #ffffff;
}

.bg-sand { background-color: var(--color-sand); }
.bg-white { background-color: #ffffff; }

.text-gold-luxury { color: var(--color-gold); font-style: italic; }

/* ANIMATIONS */
.animate-fade-up {
  opacity: 0;
  transform: translateY(30px);
  animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-400 { animation-delay: 0.4s; }

@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

/* HERO SECTION */
.hero-luxury {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 8rem 2rem 6rem;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center 30%;
  animation: heroZoom 25s ease-in-out infinite alternate;
}

@keyframes heroZoom {
  0% { transform: scale(1); }
  100% { transform: scale(1.1); }
}

.hero-overlay-luxury {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(28,32,38,0.2) 0%, rgba(28,32,38,0.7) 100%);
}

.hero-content-luxury {
  position: relative;
  z-index: 2;
  text-align: center;
  max-width: 800px;
  color: white;
}

.hero-badge-luxury {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1.5rem;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50px;
  text-transform: uppercase;
  letter-spacing: 2px;
  font-size: 0.75rem;
  font-weight: 600;
  margin-bottom: 2rem;
}

.hero-title-luxury {
  font-family: var(--font-serif);
  font-size: 4.5rem;
  font-weight: 600;
  line-height: 1.1;
  margin-bottom: 1.5rem;
  text-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.hero-subtitle-luxury {
  font-size: 1.15rem;
  line-height: 1.8;
  font-weight: 300;
  opacity: 0.9;
  max-width: 650px;
  margin: 0 auto;
}

/* SEARCH WIDGET */
.search-widget-luxury {
  position: relative;
  z-index: 3;
  margin-top: 4rem;
  width: 100%;
  max-width: 900px;
  background: white;
  border-radius: 12px;
  padding: 1.5rem 2rem;
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
}

.sw-grid-luxury {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr auto;
  gap: 1.5rem;
  align-items: flex-end;
}

.sw-field-luxury {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.sw-field-luxury label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--color-gray);
  text-transform: uppercase;
  letter-spacing: 1px;
}

.sw-field-luxury input,
.sw-field-luxury select {
  padding: 0.8rem 0;
  border: none;
  border-bottom: 1px solid #E5E7EB;
  font-size: 1rem;
  font-family: var(--font-sans);
  color: var(--color-dark);
  background: transparent;
  outline: none;
  transition: border-color 0.3s ease;
}

.sw-field-luxury input:focus,
.sw-field-luxury select:focus {
  border-bottom-color: var(--color-gold);
}

.btn-luxury-primary {
  background: var(--color-gold);
  color: white;
  border: none;
  padding: 0.9rem 2rem;
  font-family: var(--font-sans);
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 1px;
  text-transform: uppercase;
  border-radius: 4px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-luxury-primary:hover {
  background: var(--color-gold-hover);
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(201,161,92,0.3);
}

/* SECTION HEADINGS */
.section-heading-luxury {
  margin-bottom: 4rem;
}

.section-heading-luxury .subheading {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 3px;
  color: var(--color-gray);
  margin-bottom: 1rem;
}

.section-heading-luxury h2 {
  font-family: var(--font-serif);
  font-size: 3rem;
  font-weight: 600;
  color: var(--color-dark);
  margin-bottom: 1rem;
}

.section-heading-luxury p {
  font-size: 1.1rem;
  color: var(--color-gray);
}

.text-center { text-align: center; }

/* FEATURES */
.features-luxury { padding: 8rem 0 6rem; }
.features-grid-luxury {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2.5rem;
}

.feature-card-luxury {
  background: white;
  padding: 2.5rem 2rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.03);
  transition: all 0.4s ease;
  border: 1px solid #f3f4f6;
  text-align: center;
}

.feature-card-luxury:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.08);
}

.fc-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.feature-card-luxury h3 {
  font-family: var(--font-serif);
  font-size: 1.4rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.feature-card-luxury p {
  color: var(--color-gray);
  line-height: 1.6;
  font-size: 0.95rem;
}

/* ROOMS */
.rooms-preview-luxury { padding: 6rem 0; }
.rp-grid-luxury {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  margin-bottom: 3rem;
}

.rp-card-luxury {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  transition: all 0.4s ease;
}

.rp-card-luxury:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.rp-image-wrapper {
  position: relative;
  height: 300px;
  overflow: hidden;
}

.rp-image {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  transition: transform 0.8s ease;
}

.rp-card-luxury:hover .rp-image {
  transform: scale(1.08);
}

.rp-badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: white;
  color: var(--color-dark);
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  padding: 0.4rem 1rem;
  border-radius: 4px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.rp-content { padding: 2rem; }
.rp-content h3 {
  font-family: var(--font-serif);
  font-size: 1.5rem;
  margin-bottom: 0.8rem;
}

.rp-content p {
  color: var(--color-gray);
  font-size: 0.9rem;
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.rp-bottom {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.rp-price .price-val {
  display: block;
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--color-gold);
}
.rp-price .price-label {
  font-size: 0.8rem;
  color: var(--color-gray);
}

.btn-luxury-outline {
  display: inline-block;
  padding: 0.6rem 1.2rem;
  border: 1px solid var(--color-dark);
  color: var(--color-dark);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-luxury-outline:hover {
  background: var(--color-dark);
  color: white;
}

.btn-luxury-secondary {
  display: inline-block;
  padding: 1rem 2.5rem;
  background: var(--color-dark);
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 1px;
  text-decoration: none;
  border-radius: 4px;
  transition: all 0.3s ease;
}
.btn-luxury-secondary:hover { background: #333; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

/* TESTIMONIALS */
.testi-luxury {
  padding: 8rem 0;
  text-align: center;
}

.testi-wrapper {
  max-width: 800px;
  margin: 0 auto;
}

.quote-icon {
  color: var(--color-gold);
  opacity: 0.4;
  margin-bottom: 2rem;
  display: flex;
  justify-content: center;
}

.testi-active blockquote {
  font-family: var(--font-serif);
  font-size: 2rem;
  line-height: 1.5;
  color: var(--color-dark);
  margin-bottom: 2rem;
  font-style: italic;
}

.testi-author strong {
  display: block;
  font-size: 1rem;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 0.3rem;
}

.testi-author span {
  font-size: 0.9rem;
  color: var(--color-gray);
}

.testi-nav {
  display: flex;
  justify-content: center;
  gap: 0.8rem;
  margin-top: 3rem;
}

.testi-nav button {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: #cbd5e1;
  cursor: pointer;
  transition: all 0.3s ease;
}
.testi-nav button.active {
  background: var(--color-gold);
  transform: scale(1.5);
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* OFFERS */
.offers-luxury { padding: 6rem 0 8rem; }
.offers-grid-luxury {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

.offer-card-luxury {
  background: var(--color-sand);
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}
.offer-card-luxury:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.08);
}

.offer-card-inner {
  border: 1px dashed #d1d5db;
  padding: 2.5rem 2rem;
  border-radius: 4px;
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
}

.oc-badge {
  position: absolute;
  top: -0.8rem;
  left: 50%;
  transform: translateX(-50%);
  background: var(--color-gold);
  color: white;
  padding: 0.4rem 1.2rem;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.offer-card-inner h3 {
  font-family: var(--font-serif);
  font-size: 1.5rem;
  margin-top: 1rem;
  margin-bottom: 1rem;
  text-align: center;
}

.offer-card-inner p {
  color: var(--color-gray);
  text-align: center;
  line-height: 1.6;
  margin-bottom: 2rem;
  flex-grow: 1;
}

.oc-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #e5e7eb;
  padding-top: 1.5rem;
}

.oc-valid {
  font-size: 0.8rem;
  color: var(--color-gray);
  display: flex;
  align-items: center;
  gap: 0.3rem;
}

.oc-link {
  color: var(--color-gold);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  transition: gap 0.3s ease;
}
.oc-link:hover { gap: 0.7rem; }

/* FOOTER */
.footer-luxury {
  background: var(--color-dark);
  color: white;
  padding: 5rem 0 2rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 4rem;
  margin-bottom: 4rem;
}

.fb-logo {
  font-family: var(--font-serif);
  font-size: 1.8rem;
  color: var(--color-gold);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.footer-brand p {
  color: rgba(255,255,255,0.6);
  line-height: 1.8;
  max-width: 400px;
}

.footer-luxury h4 {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  color: white;
}

.footer-links a {
  display: block;
  color: rgba(255,255,255,0.6);
  text-decoration: none;
  margin-bottom: 0.8rem;
  transition: color 0.3s ease;
}
.footer-links a:hover { color: var(--color-gold); }

.footer-contact p {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  color: rgba(255,255,255,0.6);
  margin-bottom: 1rem;
  font-size: 0.9rem;
}

.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 2rem;
  text-align: center;
  color: rgba(255,255,255,0.4);
  font-size: 0.85rem;
}

/* RESPONSIVE */
@media (max-width: 900px) {
  .sw-grid-luxury { grid-template-columns: 1fr 1fr; }
  .sw-action-luxury { grid-column: 1 / -1; }
  .hero-title-luxury { font-size: 3rem; }
  .rp-grid-luxury { grid-template-columns: 1fr; max-width: 500px; margin-inline: auto; }
  .offers-grid-luxury { grid-template-columns: 1fr; max-width: 500px; margin-inline: auto; }
  .footer-grid { grid-template-columns: 1fr; gap: 2rem; }
  .section-heading-luxury h2 { font-size: 2.2rem; }
}

@media (max-width: 600px) {
  .sw-grid-luxury { grid-template-columns: 1fr; }
  .hero-title-luxury { font-size: 2.5rem; }
  .testi-active blockquote { font-size: 1.3rem; }
  .hero-luxury { padding: 6rem 1rem 4rem; }
  .search-widget-luxury { transform: translateY(0); margin-top: 2rem; }
}
</style>
