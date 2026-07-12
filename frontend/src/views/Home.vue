<template>
  <div class="home">
    <section class="hero section-dark">
      <div class="hero-bg">
        <div class="hero-image"></div>
        <div class="hero-overlay"></div>
        <div class="hero-pattern"></div>
      </div>
      <div class="hero-content">
        <div class="hero-badge">
          <SparklesIcon :size="14" />
          Resort &amp; Spa &middot; Bali
        </div>
        <h1 class="hero-title">Escape to <span class="text-gold">Paradise</span></h1>
        <p class="hero-subtitle">Nikmati liburan mewah di tengah alam tropis. Kamar premium, spa eksklusif, dan fine dining dalam satu resort bintang lima.</p>
        <div class="hero-cta">
          <router-link to="/services" class="btn btn-gold btn-lg">
            <CalendarCheckIcon :size="18" /> Pesan Sekarang
          </router-link>
          <router-link to="/about" class="btn btn-outline-light btn-lg">
            <InfoIcon :size="18" /> Lihat Fasilitas
          </router-link>
        </div>
      </div>

      <div class="search-widget">
        <div class="sw-grid">
          <div class="sw-field">
            <label>Check-in</label>
            <input v-model="searchCheckIn" type="date" :min="tomorrow" />
          </div>
          <div class="sw-field">
            <label>Check-out</label>
            <input v-model="searchCheckOut" type="date" :min="searchCheckIn || tomorrow" />
          </div>
          <div class="sw-field">
            <label>Tipe</label>
            <select v-model="searchType">
              <option value="">Semua</option>
              <option value="room">Kamar</option>
              <option value="spa">Spa</option>
              <option value="dining">Dining</option>
            </select>
          </div>
          <div class="sw-action">
            <router-link to="/services" class="btn btn-gold btn-full">
              <SearchIcon :size="16" /> Cari
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section class="features-section">
      <div class="container">
        <div class="section-heading">
          <h2>Kenapa Pilih <span class="text-accent">Kami</span>?</h2>
          <p>Kami menghadirkan pengalaman resort terlengkap untuk liburan Anda</p>
        </div>
        <div class="features-grid">
          <div v-for="(f, i) in features" :key="i" class="feature-card" :style="{ transitionDelay: i * 0.1 + 's' }">
            <div class="feature-icon" :style="{ background: f.bg }">
              <component :is="f.icon" :size="24" />
            </div>
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <section class="rooms-preview section-dark">
      <div class="container">
        <div class="section-heading light">
          <h2>Kamar <span class="text-gold">Premium</span></h2>
          <p>Pilih kamar impian Anda dengan pemandangan terbaik</p>
        </div>
        <div class="rp-grid">
          <div class="rp-card" v-for="(r, i) in roomPreviews" :key="i">
            <div class="rp-img" :style="{ backgroundImage: 'url(' + r.img + ')' }">
              <div class="rp-badge">{{ r.badge }}</div>
            </div>
            <div class="rp-body">
              <h3>{{ r.name }}</h3>
              <p>{{ r.desc }}</p>
              <div class="rp-footer">
                <span class="rp-price">{{ r.price }}</span>
                <router-link :to="'/rooms?type=' + r.slug" class="btn btn-sm btn-gold">Lihat</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div v-for="(s, i) in stats" :key="i" class="stat-item">
            <span class="stat-number">{{ s.number }}</span>
            <span class="stat-label">{{ s.label }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="gallery-section">
      <div class="container">
        <div class="section-heading">
          <h2>Galeri <span class="text-accent">Resort</span></h2>
          <p>Momen terbaik dari sudut-sudut favorit resort kami</p>
        </div>
        <div class="gallery-grid">
          <div v-for="(g, i) in gallery" :key="i" class="gallery-item">
            <div class="gallery-img" :style="{ backgroundImage: 'url(' + g.img + ')' }">
              <div class="gallery-caption">{{ g.label }}</div>
            </div>
          </div>
        </div>
        <div class="text-center mt-2">
          <router-link to="/gallery" class="btn btn-primary">
            <ImageIcon :size="16" /> Lihat Semua Foto
          </router-link>
        </div>
      </div>
    </section>

    <section class="testi-section section-dark">
      <div class="container">
        <div class="section-heading light">
          <h2>Apa Kata <span class="text-gold">Tamu</span></h2>
          <p>Pengalaman nyata dari tamu yang telah menginap</p>
        </div>
        <div class="testi-slider">
          <div class="testi-track" :style="{ transform: 'translateX(-' + testiIndex * 100 + '%)' }">
            <div v-for="(t, i) in testimonials" :key="i" class="testi-card">
              <div class="star-rating lg">
                <StarIcon v-for="n in 5" :key="n" :size="20" class="star-filled" />
              </div>
              <blockquote>"{{ t.quote }}"</blockquote>
              <div class="testi-author">
                <div class="testi-avatar">{{ t.name[0] }}</div>
                <div>
                  <strong>{{ t.name }}</strong>
                  <span>{{ t.visit }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="testi-dots">
          <button v-for="(t, i) in testimonials" :key="i" :class="{ active: testiIndex === i }" @click="testiIndex = i"></button>
        </div>
      </div>
    </section>

    <section class="offers-section">
      <div class="container">
        <div class="section-heading">
          <h2>Penawaran <span class="text-accent">Spesial</span></h2>
          <p>Promo terbatas untuk pengalaman yang lebih berkesan</p>
        </div>
        <div class="offers-grid">
          <div v-for="(o, i) in specialOffers" :key="i" class="offer-card" :class="'offer-' + (i + 1)">
            <div class="offer-badge" :class="o.badgeClass">{{ o.badge }}</div>
            <div class="offer-body">
              <h3>{{ o.title }}</h3>
              <p>{{ o.desc }}</p>
              <div class="offer-meta">
                <span><ClockIcon :size="12" /> {{ o.valid }}</span>
                <router-link to="/services" class="btn btn-sm btn-gold">Pesan</router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="footer section-dark">
      <div class="container">
        <div class="footer-grid">
          <div class="footer-brand">
            <div class="fb-icon">
              <TreePineIcon :size="20" />
            </div>
            <h3>Tropical Resort</h3>
            <p>Resort &amp; Spa &middot; Bali</p>
            <div class="fb-social">
              <a href="#" aria-label="Instagram"><GlobeIcon :size="18" /></a>
              <a href="#" aria-label="Facebook"><HeartIcon :size="18" /></a>
              <a href="#" aria-label="Twitter"><Share2Icon :size="18" /></a>
            </div>
          </div>
          <div class="footer-links">
            <h4>Layanan</h4>
            <router-link to="/rooms">Kamar</router-link>
            <router-link to="/services?kategori=spa">Spa</router-link>
            <router-link to="/services?kategori=dining">Dining</router-link>
            <router-link to="/services">Semua Layanan</router-link>
          </div>
          <div class="footer-links">
            <h4>Informasi</h4>
            <router-link to="/about">Tentang</router-link>
            <router-link to="/gallery">Galeri</router-link>
            <router-link to="/offers">Promo</router-link>
            <router-link to="/faq">FAQ</router-link>
          </div>
          <div class="footer-contact">
            <h4>Kontak</h4>
            <p><MapPinIcon :size="12" /> Jl. Tropis Indah No. 1, Bali</p>
            <p><PhoneIcon :size="12" /> +62 812 3456 7890</p>
            <p><MailIcon :size="12" /> info@tropicalresort.com</p>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2026 Tropical Resort. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import {
  SparklesIcon, StarIcon, CalendarCheckIcon, InfoIcon, SearchIcon,
  DropletsIcon, UtensilsCrossedIcon, LeafIcon, WavesIcon, SunIcon, TreePineIcon,
  MapPinIcon, PhoneIcon, MailIcon, ClockIcon, ImageIcon,
  GlobeIcon, HeartIcon, Share2Icon
} from '@lucide/vue'

const tomorrow = computed(() => {
  const d = new Date()
  d.setDate(d.getDate() + 1)
  return d.toISOString().split('T')[0]
})

const searchCheckIn = ref('')
const searchCheckOut = ref('')
const searchType = ref('')
const testiIndex = ref(0)

const features = [
  { icon: DropletsIcon, title: 'Infinity Pool', desc: 'Kolam renang tanpa batas dengan panorama laut lepas, dikelilingi kursi santai dan payung tropis.', bg: 'linear-gradient(135deg, rgba(16,185,129,0.12), rgba(6,148,162,0.12))' },
  { icon: SparklesIcon, title: 'Spa & Wellness', desc: 'Perawatan tubuh tradisional Bali dan modern oleh terapis profesional di ruang spa privat.', bg: 'linear-gradient(135deg, rgba(201,161,92,0.12), rgba(236,201,75,0.12))' },
  { icon: UtensilsCrossedIcon, title: 'Fine Dining', desc: 'Restoran dengan menu internasional, masakan khas Bali, dan private dining di tepi kolam.', bg: 'linear-gradient(135deg, rgba(239,68,68,0.12), rgba(251,146,60,0.12))' },
  { icon: LeafIcon, title: 'Taman Tropis', desc: 'Area hijau seluas 2 hektar dengan jalur jogging, gazebo, dan koleksi tanaman tropis langka.', bg: 'linear-gradient(135deg, rgba(34,197,94,0.12), rgba(74,222,128,0.12))' },
  { icon: SunIcon, title: 'Private Beach', desc: 'Pantai pribadi berpasir putih dengan water sports, snorkeling, dan area sunbathing eksklusif.', bg: 'linear-gradient(135deg, rgba(251,191,36,0.12), rgba(245,158,11,0.12))' },
  { icon: WavesIcon, title: 'Sunset Cruise', desc: 'Pelayaran sore eksklusif mengelilingi teluk sambil menikmati hidangan ringan dan koktail.', bg: 'linear-gradient(135deg, rgba(168,85,247,0.12), rgba(236,72,153,0.12))' },
]

const roomPreviews = [
  { name: 'Deluxe Ocean Suite', desc: 'Kamar luas dengan balkon pribadi menghadap langsung ke laut lepas.', price: 'Rp 2.500.000/malam', badge: 'Best Seller', slug: 'deluxe', img: '/images/room-deluxe.jpg' },
  { name: 'Premium Garden Villa', desc: 'Villa pribadi di tengah taman tropis dengan kolam renang eksklusif.', price: 'Rp 4.200.000/malam', badge: 'Premium', slug: 'premium', img: '/images/villa-pool.jpg' },
  { name: 'Family Suite', desc: 'Suite dua kamar dengan ruang keluarga luas, cocok untuk liburan keluarga.', price: 'Rp 3.800.000/malam', badge: 'Family', slug: 'family', img: '/images/room-family.jpg' },
]

const stats = [
  { number: '50+', label: 'Kamar Premium' },
  { number: '5', label: 'Restoran' },
  { number: '98%', label: 'Kepuasan Tamu' },
  { number: '12', label: 'Tahun Berdiri' },
]

const gallery = [
  { label: 'Kolam Renang Infinity', img: '/images/pool-aerial.jpg' },
  { label: 'Kamar Deluxe Suite', img: '/images/suite-living.jpg' },
  { label: 'Restoran Tepi Pantai', img: '/images/restaurant.jpg' },
  { label: 'Spa Tradisional', img: '/images/spa.jpg' },
  { label: 'Sunset di Private Beach', img: '/images/sunset-beach.jpg' },
  { label: 'Taman Tropis Resort', img: '/images/garden.jpg' },
]

const testimonials = [
  { name: 'Sarah Wijaya', quote: 'Pengalaman menginap yang luar biasa. Pemandangan dari Suite Room bikin takjub! Pelayanan ramah dan fasilitas lengkap.', visit: 'Menginap 3 malam, Maret 2026' },
  { name: 'Budi Santoso', quote: 'Spa-nya juara! Aromatherapy khas Bali bikin badan terasa seperti lahir kembali. Pasti balik lagi.', visit: 'Menginap 2 malam, April 2026' },
  { name: 'Maya Putri', quote: 'Private dinner di tepi kolam — romantis banget. Recommended banget buat honeymoon atau anniversary!', visit: 'Menginap 4 malam, Februari 2026' },
  { name: 'Andre Kurniawan', quote: 'Resort cocok banget buat quality time keluarga. Anak-anak betah di kids club dan kolam renang.', visit: 'Menginap 5 malam, Januari 2026' },
]

const specialOffers = [
  { title: 'Honeymoon Package', desc: 'Suite Room 3 malam + candle light dinner + spa couple + bunga segar setiap hari', badge: '20% OFF', badgeClass: 'badge-warm', valid: 'Berlaku s.d 30 Sep 2026' },
  { title: 'Stay 4 Pay 3', desc: 'Beli 4 malam, bayar 3 malam untuk semua tipe kamar. Termasuk sarapan gratis!', badge: 'BONUS', badgeClass: 'badge-gold', valid: 'Berlaku s.d 31 Des 2026' },
  { title: 'Spa & Dine Combo', desc: 'Paket spa 2 jam + dinner 3-course with wine pairing dengan harga spesial', badge: '35% OFF', badgeClass: 'badge-warm', valid: 'Berlaku s.d 15 Okt 2026' },
]

let testiTimer
onMounted(() => {
  testiTimer = setInterval(() => {
    testiIndex.value = (testiIndex.value + 1) % testimonials.length
  }, 5000)
})
onUnmounted(() => {
  clearInterval(testiTimer)
})
</script>

<style scoped>
.home { overflow-x: hidden; }

.hero {
  position: relative;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 6rem 2rem 4rem;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.hero-image {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background-image: url('/images/pool-hero.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  animation: hero-zoom 20s ease-in-out infinite alternate;
}

@keyframes hero-zoom {
  0% { transform: scale(1); }
  100% { transform: scale(1.08); }
}

.hero-overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    linear-gradient(135deg, rgba(8,12,28,0.7) 0%, rgba(8,12,28,0.4) 50%, transparent 100%),
    linear-gradient(to top, rgba(8,12,28,0.85) 0%, transparent 50%);
}

.hero-pattern {
  position: absolute;
  bottom: -2px;
  left: 0;
  right: 0;
  height: 120px;
  pointer-events: none;
  background: #0F1117;
  clip-path: ellipse(70% 100% at 50% 100%);
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  max-width: 750px;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 1.2rem;
  border-radius: 9999px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.08);
  color: rgba(255,255,255,0.7);
  font-size: 0.8rem;
  font-weight: 500;
  margin-bottom: 1.5rem;
  backdrop-filter: blur(8px);
}

.hero-title {
  font-size: 4rem;
  font-weight: 800;
  color: white;
  line-height: 1.1;
  margin-bottom: 1.25rem;
  letter-spacing: -0.02em;
}

.text-gold { color: #C9A15C; }

.hero-subtitle {
  color: rgba(255,255,255,0.55);
  font-size: 1.125rem;
  line-height: 1.7;
  margin-bottom: 2rem;
}

.hero-cta {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.hero-cta .btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.search-widget {
  position: relative;
  z-index: 2;
  margin-top: 2rem;
  width: 100%;
  max-width: 800px;
  background: rgba(255,255,255,0.04);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 20px;
  padding: 1.5rem;
  box-shadow: 0 12px 48px rgba(0,0,0,0.4);
  transform: translateY(60px);
}

.sw-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr auto;
  gap: 1rem;
  align-items: end;
}

.sw-field {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.sw-field label {
  font-size: 0.7rem;
  font-weight: 600;
  color: rgba(255,255,255,0.35);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.sw-field input,
.sw-field select {
  padding: 0.65rem 1rem;
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  font-size: 0.85rem;
  background: rgba(255,255,255,0.04);
  color: rgba(255,255,255,0.85);
  transition: border-color 0.2s ease;
  outline: none;
}

.sw-field input:focus,
.sw-field select:focus {
  border-color: #C9A15C;
  box-shadow: 0 0 0 3px rgba(201,161,92,0.12);
}

.sw-field input::placeholder,
.sw-field select::placeholder { color: rgba(255,255,255,0.2); }

.sw-field select option { color: #111; background: #fff; }

.sw-action { min-width: 110px; }

.sw-action .btn {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  height: 100%;
  justify-content: center;
}

@media (max-width: 700px) {
  .sw-grid { grid-template-columns: 1fr 1fr; }
  .sw-action { grid-column: 1 / -1; }
}

.features-section { padding: 6rem 0 5rem; background: #0F1117; }

.section-heading {
  text-align: center;
  max-width: 600px;
  margin: 0 auto 3rem;
}

.section-heading h2 {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
}

.section-heading p {
  color: rgba(255,255,255,0.4);
  font-size: 1rem;
}

.section-heading.light h2 { color: white; }
.section-heading.light p { color: rgba(255,255,255,0.45); }

.text-accent { color: #2D5A45; }

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.feature-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.04);
  border-radius: 20px;
  padding: 2rem;
  transition: all 0.3s ease;
  opacity: 0;
  transform: translateY(30px);
  animation: fadeUp 0.6s ease forwards;
}

@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

.feature-card:hover {
  background: rgba(255,255,255,0.05);
  border-color: rgba(255,255,255,0.08);
  transform: translateY(-4px);
}

.feature-icon {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
  color: white;
}

.feature-card h3 { color: white; font-size: 1.1rem; margin-bottom: 0.5rem; }
.feature-card p { color: rgba(255,255,255,0.4); font-size: 0.85rem; line-height: 1.6; }

.rooms-preview { padding: 5rem 0; background: #0A0C18; }

.rp-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.rp-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.04);
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
}

.rp-card:hover {
  transform: translateY(-6px);
  border-color: rgba(255,255,255,0.08);
}

.rp-img {
  height: 200px;
  background-size: cover;
  background-position: center;
  position: relative;
}

.rp-badge {
  position: absolute;
  top: 0.75rem;
  left: 0.75rem;
  padding: 0.25rem 0.75rem;
  background: rgba(201,161,92,0.9);
  color: #fff;
  font-size: 0.7rem;
  font-weight: 600;
  border-radius: 9999px;
  backdrop-filter: blur(4px);
}

.rp-body { padding: 1.5rem; }

.rp-body h3 { color: white; font-size: 1rem; margin-bottom: 0.4rem; }
.rp-body p { color: rgba(255,255,255,0.4); font-size: 0.8rem; line-height: 1.5; margin-bottom: 1rem; }

.rp-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.rp-price {
  font-weight: 700;
  font-size: 0.85rem;
  color: #C9A15C;
}

.stats-section {
  padding: 4rem 0;
  background: #0F1117;
  border-top: 1px solid rgba(255,255,255,0.02);
  border-bottom: 1px solid rgba(255,255,255,0.02);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  text-align: center;
}

.stat-item { display: flex; flex-direction: column; gap: 0.25rem; }

.stat-number {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  letter-spacing: -0.02em;
}

.stat-label {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.35);
  font-weight: 500;
}

.gallery-section { padding: 5rem 0; background: #0F1117; }

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
}

.gallery-item {
  border-radius: 16px;
  overflow: hidden;
  aspect-ratio: 4/3;
  cursor: pointer;
  animation: fadeUp 0.6s ease forwards;
}

.gallery-img {
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  transition: transform 0.5s ease;
  position: relative;
  display: flex;
  align-items: flex-end;
}

.gallery-item:hover .gallery-img {
  transform: scale(1.08);
}

.gallery-caption {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
  color: white;
  font-size: 0.8rem;
  font-weight: 600;
}

@media (max-width: 600px) {
  .gallery-grid { grid-template-columns: repeat(2, 1fr); }
}

.testi-section { padding: 5rem 0; background: #0A0C18; }

.testi-slider { overflow: hidden; max-width: 650px; margin: 0 auto; }

.testi-track { display: flex; transition: transform 0.6s ease; }

.testi-card {
  min-width: 100%;
  text-align: center;
  padding: 2rem 1.5rem;
}

.star-rating {
  display: flex;
  justify-content: center;
  gap: 0.3rem;
  margin-bottom: 1rem;
}

.star-filled { color: #C9A15C; fill: #C9A15C; }

.testi-card blockquote {
  font-size: 1.25rem;
  line-height: 1.7;
  color: rgba(255,255,255,0.8);
  font-weight: 500;
  font-style: italic;
  margin-bottom: 1.5rem;
}

.testi-author {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.testi-avatar {
  width: 44px;
  height: 44px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.testi-author div strong { display: block; color: white; font-size: 0.85rem; }
.testi-author div span { display: block; font-size: 0.75rem; color: rgba(255,255,255,0.35); }

.testi-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

.testi-dots button {
  width: 10px;
  height: 10px;
  border-radius: 9999px;
  border: 1px solid rgba(255,255,255,0.15);
  background: transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}

.testi-dots button.active {
  background: #C9A15C;
  border-color: #C9A15C;
  width: 28px;
  border-radius: 5px;
}

.offers-section { padding: 5rem 0; background: #0F1117; }

.offers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.offer-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.04);
  border-radius: 20px;
  overflow: hidden;
  position: relative;
  transition: all 0.3s ease;
  animation: fadeUp 0.6s ease forwards;
}

.offer-card:hover {
  background: rgba(255,255,255,0.05);
  border-color: rgba(201,161,92,0.2);
  transform: translateY(-4px);
}

.offer-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.3rem 0.8rem;
  border-radius: 9999px;
  font-size: 0.7rem;
  font-weight: 700;
}

.badge-warm { background: rgba(239,68,68,0.15); color: #EF4444; }
.badge-gold { background: rgba(201,161,92,0.15); color: #C9A15C; }

.offer-body { padding: 1.75rem; }

.offer-body h3 { color: white; font-size: 1.1rem; margin-bottom: 0.5rem; }
.offer-body p { color: rgba(255,255,255,0.4); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1rem; }

.offer-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.3rem;
  font-size: 0.75rem;
  color: rgba(255,255,255,0.3);
}

.offer-meta span { display: inline-flex; align-items: center; gap: 0.25rem; }

.footer { padding: 4rem 0 2rem; background: #080C1C; }

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1.5fr;
  gap: 2rem;
  margin-bottom: 3rem;
}

.fb-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  border-radius: 12px;
  color: white;
  margin-bottom: 0.75rem;
}

.footer-brand h3 { color: white; font-size: 1.25rem; }
.footer-brand p { color: #C9A15C; font-size: 0.85rem; margin-top: 0.25rem; }

.fb-social {
  display: flex;
  gap: 0.75rem;
  margin-top: 1rem;
}

.fb-social a {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.04);
  border-radius: 10px;
  color: rgba(255,255,255,0.35);
  transition: all 0.2s ease;
}

.fb-social a:hover {
  background: rgba(201,161,92,0.15);
  color: #C9A15C;
}

.footer-links h4,
.footer-contact h4 {
  font-size: 0.8rem;
  color: rgba(255,255,255,0.5);
  margin-bottom: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.footer-links a,
.footer-contact p {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  color: rgba(255,255,255,0.3);
  font-size: 0.85rem;
  margin-bottom: 0.35rem;
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-links a:hover { color: #C9A15C; }

.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.04);
  padding-top: 1.5rem;
  text-align: center;
}

.footer-bottom p { color: rgba(255,255,255,0.4); font-size: 0.75rem; }

@media (max-width: 768px) {
  .hero-title { font-size: 2.5rem; }
  .hero-subtitle { font-size: 1rem; }
  .hero-cta { flex-direction: column; align-items: center; }
  .hero-cta .btn { width: 100%; max-width: 280px; }
  .search-widget { padding: 1rem; transform: translateY(40px); }
  .sw-grid { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
  .sw-action { grid-column: 1 / -1; }
  .features-section { padding: 4rem 0 3rem; }
  .features-grid { grid-template-columns: 1fr; }
  .rp-grid { grid-template-columns: 1fr; }
  .rp-img { height: 220px; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
  .gallery-section { padding: 3rem 0; }
  .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
  .testi-section { padding: 3rem 0; }
  .testi-card blockquote { font-size: 1.05rem; }
  .offers-section { padding: 3rem 0; }
  .offers-grid { grid-template-columns: 1fr; }
  .footer { padding: 2.5rem 0 1.5rem; }
  .footer-grid { grid-template-columns: 1fr; gap: 1.5rem; }
  .section-heading h2 { font-size: 1.75rem; }
}

@media (max-width: 480px) {
  .hero-title { font-size: 1.75rem; }
  .hero { min-height: 85vh; padding: 5rem 1.25rem 3rem; }
  .hero-subtitle { font-size: 0.9rem; }
  .hero-badge { font-size: 0.7rem; }
  .sw-grid { grid-template-columns: 1fr; }
  .search-widget { transform: translateY(30px); }
  .gallery-grid { grid-template-columns: 1fr 1fr; }
  .stats-grid { gap: 1rem; }
  .stat-number { font-size: 1.75rem; }
}
</style>
