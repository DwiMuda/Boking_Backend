<template>
  <div class="about-page">
    <section class="about-hero section-dark">
      <div class="container text-center">
        <h1>Tentang <span class="text-gold">Kami</span></h1>
        <p>Resort tropis terbaik di Bali sejak 2010</p>
      </div>
    </section>

    <section class="about-story">
      <div class="container-sm">
        <div class="story-layout" v-observe>
          <div class="story-image">
            <div class="story-placeholder" style="background-image: url('/images/garden-path.jpg');"></div>
          </div>
          <div class="story-text">
            <h2>Cerita <span class="text-accent">Kami</span></h2>
            <p>Berawal dari kecintaan pada alam dan budaya Bali, Tropical Resort didirikan pada tahun 2010 dengan visi menjadi resort ramah lingkungan yang memberikan pengalaman liburan autentik.</p>
            <p>Dengan luas 5 hektar, resort kami memiliki 23 kamar, spa, restoran, kolam renang infinity, dan taman tropis yang dirancang untuk menyatu dengan alam sekitar.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="about-stats section-dark">
      <div class="container">
        <div class="stats-row">
          <div class="stats-item" v-observe>
            <div class="stat-count text-gold">15+</div>
            <p>Tahun Berdiri</p>
          </div>
          <div class="stats-item" v-observe>
            <div class="stat-count text-gold">23</div>
            <p>Kamar</p>
          </div>
          <div class="stats-item" v-observe>
            <div class="stat-count text-gold">4.8</div>
            <p>Rating Tamu</p>
          </div>
          <div class="stats-item" v-observe>
            <div class="stat-count text-gold">7</div>
            <p>Fasilitas Unggulan</p>
          </div>
        </div>
      </div>
    </section>

    <section class="about-facilities">
      <div class="container">
        <div class="section-heading" v-observe>
          <h2>Fasilitas <span class="text-accent">Kami</span></h2>
          <p>Semua kebutuhan liburan Anda tersedia dalam satu resort</p>
        </div>
        <div class="facilities-grid">
          <div v-for="(f, i) in facilities" :key="i" class="facility-card" v-observe :style="{ transitionDelay: `${i*0.08}s` }">
            <component :is="f.icon" :size="28" class="fc-icon" />
            <h3>{{ f.title }}</h3>
            <p>{{ f.desc }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { DropletsIcon, SparklesIcon, UtensilsIcon, TreePineIcon, WavesIcon, DumbbellIcon, CarIcon } from '@lucide/vue'

const facilities = [
  { icon: DropletsIcon, title: 'Infinity Pool', desc: 'Kolam renang panorama laut' },
  { icon: SparklesIcon, title: 'Spa & Wellness', desc: 'Perawatan tradisional & modern' },
  { icon: UtensilsIcon, title: 'Restoran & Bar', desc: 'Fine dining & cocktail bar' },
  { icon: TreePineIcon, title: 'Taman Tropis', desc: 'Area hijau 2 hektar' },
  { icon: WavesIcon, title: 'Private Beach', desc: 'Pantai pribadi pasir putih' },
  { icon: DumbbellIcon, title: 'Fitness Center', desc: 'Gym 24 jam' },
  { icon: CarIcon, title: 'Shuttle Service', desc: 'Antar-jemput bandara' },
]
</script>

<style scoped>
.about-hero { padding: 6rem 2rem 4rem; }
.about-hero h1 { font-size: var(--text-4xl); color: var(--text-on-dark); margin-bottom: 0.75rem; }
.about-hero p { color: rgba(255,255,255,0.6); }

.about-story { padding: 5rem 0; }
.story-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; }
.story-image .story-placeholder { border-radius: var(--radius-lg); aspect-ratio: 4/3; background-size: cover; background-position: center; }
.story-text h2 { font-size: var(--text-3xl); margin-bottom: 1.25rem; }
.story-text p { color: var(--text-muted); line-height: var(--leading-relaxed); margin-bottom: 1rem; }

.about-stats { padding: 4rem 0; }
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; text-align: center; }
.stats-item .stat-count { font-size: var(--text-5xl); font-weight: var(--weight-extrabold); margin-bottom: 0.5rem; }
.stats-item p { color: rgba(255,255,255,0.6); font-size: var(--text-sm); }

.about-facilities { padding: 5rem 0; }
.facilities-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem; }
.facility-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 1.75rem;
  transition: all var(--duration-normal) var(--ease-smooth);
}
.facility-card:hover { border-color: var(--accent-primary-muted); box-shadow: var(--shadow-md); transform: translateY(-2px); }
.fc-icon { color: var(--accent-primary); margin-bottom: 1rem; }
.facility-card h3 { font-size: var(--text-lg); margin-bottom: 0.35rem; }
.facility-card p { color: var(--text-muted); font-size: var(--text-sm); }

@media (max-width: 700px) {
  .story-layout { grid-template-columns: 1fr; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>
