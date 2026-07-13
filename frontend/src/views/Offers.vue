<template>
  <div class="offers-page">
    <section class="offers-hero section-dark">
      <div class="container text-center">
        <div class="flex items-center justify-between mb-1">
          <button class="btn btn-outline-light btn-sm" @click="$router.push('/')">
            <ArrowLeftIcon :size="18" />
            Kembali
          </button>
          <h1 class="mb-0">Penawaran <span class="text-gold">Spesial</span></h1>
          <span></span>
        </div>
        <p>Promo terbatas untuk pengalaman yang lebih berkesan</p>
      </div>
    </section>

    <section class="offers-list">
      <div class="container">
        <div class="offers-grid">
          <div v-for="(o, i) in offers" :key="i" class="offer-full-card" v-observe :style="{ transitionDelay: `${i*0.1}s` }">
            <div class="offer-badge" :class="o.badgeClass">{{ o.badge }}</div>
            <div class="offer-body">
              <h3>{{ o.title }}</h3>
              <p>{{ o.desc }}</p>
              <div class="offer-details">
                <div class="offer-meta"><CalendarIcon :size="14" /> {{ o.valid }}</div>
                <div class="offer-price">Mulai Rp {{ formatPrice(o.price) }}</div>
              </div>
              <router-link v-if="o.link" :to="o.link" class="btn btn-gold btn-sm mt-1">Pesan Sekarang</router-link>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ArrowLeftIcon, CalendarIcon } from '@lucide/vue'

const offers = [
  { title: 'Honeymoon Package', desc: 'Paket romantis untuk pasangan. Termasuk Suite Room 3 malam, candle light dinner, spa couple, dan bunga segar setiap hari.', badge: '20% OFF', badgeClass: 'badge-warm', valid: 'Berlaku s.d 30 Sep 2026', price: 3600000, link: '/rooms' },
  { title: 'Stay 4 Pay 3', desc: 'Beli 4 malam dan bayar 3 malam untuk semua tipe kamar. Cocok untuk liburan panjang bersama keluarga.', badge: 'BONUS', badgeClass: 'badge-gold', valid: 'Berlaku s.d 31 Des 2026', price: 1500000, link: '/rooms' },
  { title: 'Spa & Dine Combo', desc: 'Paket relaksasi lengkap: spa 2 jam pilihan + dinner 3-course untuk 2 orang. Harga spesial.', badge: '35% OFF', badgeClass: 'badge-warm', valid: 'Berlaku s.d 15 Okt 2026', price: 425000, link: '/services' },
  { title: 'Early Bird Discount', desc: 'Booking 30 hari sebelumnya dan dapatkan diskon 15% untuk semua tipe kamar.', badge: '15% OFF', badgeClass: 'badge-gold', valid: 'Sepanjang tahun 2026', price: 425000, link: '/rooms' },
  { title: 'Family Getaway', desc: 'Paket keluarga 4-6 orang termasuk Family Room atau Villa, breakfast, dan kids club.', badge: '25% OFF', badgeClass: 'badge-warm', valid: 'Berlaku s.d 30 Nov 2026', price: 2250000, link: '/rooms' },
  { title: 'Birthday Special', desc: 'Rayakan ulang tahun di resort kami! Dapatkan kue ulang tahun gratis + spa voucher.', badge: 'GRATIS', badgeClass: 'badge-gold', valid: 'Berlaku s.d 31 Des 2026', price: 0, link: '/services' },
]

function formatPrice(p) {
  return new Intl.NumberFormat('id-ID').format(p)
}
</script>

<style scoped>
.offers-hero { padding: 6rem 2rem 4rem; }
.offers-hero h1 { font-size: var(--text-4xl); color: var(--text-on-dark); margin-bottom: 0.75rem; }
.offers-hero p { color: rgba(255,255,255,0.6); }

.offers-list { padding: 5rem 0; }
.offers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem; }

.offer-full-card {
  position: relative;
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
  transition: all var(--duration-normal) var(--ease-smooth);
}

.offer-full-card:hover {
  border-color: var(--border-default);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.offer-badge {
  position: absolute;
  top: 1rem;
  right: 1rem;
  padding: 0.35rem 0.9rem;
  border-radius: var(--radius-full);
  font-size: var(--text-xs);
  font-weight: var(--weight-bold);
  font-family: var(--font-heading);
}

.badge-warm { background: var(--accent-warm-muted); color: var(--accent-warm); }
.badge-gold { background: var(--accent-gold-muted); color: var(--accent-gold); }

.offer-body { padding: 1.75rem; }
.offer-body h3 { font-size: var(--text-xl); margin-bottom: 0.5rem; }
.offer-body p { color: var(--text-muted); line-height: var(--leading-relaxed); margin-bottom: 1rem; }

.offer-details {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.offer-meta { display: flex; align-items: center; gap: 0.35rem; color: var(--text-muted); font-size: var(--text-sm); }
.offer-meta svg { color: var(--accent-primary); }
.offer-price { font-family: var(--font-heading); font-weight: var(--weight-bold); color: var(--accent-warm); font-size: var(--text-sm); }

.mb-0 { margin-bottom: 0; }
</style>