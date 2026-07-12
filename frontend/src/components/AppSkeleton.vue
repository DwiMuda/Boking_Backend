<template>
  <div v-if="type === 'card'" class="skeleton-card-grid">
    <div v-for="i in count" :key="i" class="skeleton-card">
      <div class="skeleton skeleton-img"></div>
      <div class="skeleton skeleton-line" style="width:75%"></div>
      <div class="skeleton skeleton-line"></div>
      <div class="skeleton skeleton-line" style="width:45%"></div>
    </div>
  </div>
  <div v-else-if="type === 'table-row'" class="skeleton-table">
    <div v-for="i in count" :key="i" class="skeleton-table-row">
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
      <div class="skeleton"></div>
    </div>
  </div>
  <div v-else class="skeleton-text">
    <div v-for="i in count" :key="i">
      <div class="skeleton" style="height:14px;width:100%;margin-bottom:0.5rem;border-radius:var(--radius-sm)"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  type: { type: String, default: 'text' },
  count: { type: Number, default: 3 }
})
</script>

<style scoped>
.skeleton-card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
.skeleton-table {
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  overflow: hidden;
}
.skeleton-table-row {
  display: grid;
  grid-template-columns: 1fr 2fr 1.5fr 1fr 1fr 1fr 1fr;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  align-items: center;
  border-bottom: 1px solid var(--border-subtle);
}
.skeleton-table-row:last-child { border-bottom: none; }
</style>
