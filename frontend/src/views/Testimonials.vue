<template>
  <div class="testi-page">
    <section class="testi-hero section-dark">
      <div class="container text-center">
        <h1>Testimoni <span class="text-gold">Tamu</span></h1>
        <p>Pengalaman nyata dari tamu yang telah menginap</p>
      </div>
    </section>

    <section class="testi-list">
      <div class="container">
        <div class="testi-grid">
          <div v-for="(t, i) in reviews" :key="i" class="testi-card" v-observe :style="{ transitionDelay: `${i*0.08}s` }">
            <div class="star-rating lg" v-html="renderStars(t.rating)"></div>
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
    </section>
  </div>
</template>

<script setup>
const reviews = [
  { name: 'Sarah Wijaya', quote: 'Pemandangan dari Suite Room bikin takjub! Pelayanan ramah, makanan enak. Pasti balik lagi.', rating: 5, visit: 'Suite Room - 3 malam' },
  { name: 'Budi Santoso', quote: 'Spa-nya kelas dunia. Aromatherapy dan body scrub bikin badan segar kembali.', rating: 5, visit: 'Deluxe Room - 2 malam' },
  { name: 'Maya Putri', quote: 'Private dinner di tepi kolam romantis banget. Suami saya sampai speechless.', rating: 5, visit: 'Suite Room - 4 malam' },
  { name: 'Andre Kurniawan', quote: 'Anak-anak betah di Family Room. Kolam renangnya luas dan bersih.', rating: 4, visit: 'Family Room - 5 malam' },
  { name: 'Dewi Lestari', quote: 'Villa dengan kolam pribadi — pengalaman menginap terbaik yang pernah saya rasakan.', rating: 5, visit: 'Villa - 3 malam' },
  { name: 'Rizky Pratama', quote: 'Perfect honeymoon destination! Every detail was well thought out.', rating: 5, visit: 'Suite Room - 4 malam' },
  { name: 'Fitri Handayani', quote: 'Konsep eco-resort-nya keren. Mereka pakai solar panel dan waste management.', rating: 4, visit: 'Standard Room - 2 malam' },
  { name: 'Hendra Gunawan', quote: 'Breakfast buffet-nya luar biasa. Pilihan makanannya banyak dan berkualitas.', rating: 5, visit: 'Deluxe Room - 3 malam' },
]

function renderStars(n) {
  return '★'.repeat(n) + '☆'.repeat(5 - n)
}
</script>

<style scoped>
.testi-hero { padding: 6rem 2rem 4rem; }
.testi-hero h1 { font-size: var(--text-4xl); color: var(--text-on-dark); margin-bottom: 0.75rem; }
.testi-hero p { color: rgba(255,255,255,0.6); }

.testi-list { padding: 5rem 0; }
.testi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }

.testi-card {
  background: var(--bg-surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  padding: 2rem;
  transition: all var(--duration-normal) var(--ease-smooth);
}

.testi-card:hover {
  border-color: var(--border-default);
  box-shadow: var(--shadow-md);
  transform: translateY(-2px);
}

.testi-card .star-rating { margin-bottom: 1rem; display: block; }
.testi-card blockquote { font-style: italic; line-height: var(--leading-relaxed); color: var(--text-secondary); margin-bottom: 1.5rem; }
.testi-author { display: flex; align-items: center; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle); }
.testi-avatar {
  width: 40px; height: 40px; border-radius: var(--radius-full);
  background: var(--accent-primary-muted); color: var(--accent-primary);
  display: flex; align-items: center; justify-content: center;
  font-weight: var(--weight-bold); font-family: var(--font-heading);
}
.testi-author div strong { display: block; font-size: var(--text-sm); }
.testi-author div span { font-size: var(--text-xs); color: var(--text-muted); }
</style>
