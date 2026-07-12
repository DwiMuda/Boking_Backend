<template>
  <div class="empty-state-wrapper">
    <!-- No bookings -->
    <svg v-if="type === 'booking'" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="empty-illustration">
      <rect x="3" y="4" width="18" height="18" rx="2" ry="2" opacity="0.15"/>
      <line x1="16" y1="2" x2="16" y2="6" opacity="0.4"/>
      <line x1="8" y1="2" x2="8" y2="6" opacity="0.4"/>
      <line x1="3" y1="10" x2="21" y2="10" opacity="0.4"/>
      <path d="M8 14h.01" stroke="currentColor" stroke-width="2"/>
      <path d="M12 14h.01" stroke="currentColor" stroke-width="2"/>
      <path d="M16 14h.01" stroke="currentColor" stroke-width="2"/>
      <path d="M8 18h.01" stroke="currentColor" stroke-width="2"/>
      <path d="M12 18h.01" stroke="currentColor" stroke-width="2"/>
      <path d="M16 18h.01" stroke="currentColor" stroke-width="2"/>
    </svg>
    <!-- No search results -->
    <svg v-else-if="type === 'search'" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="empty-illustration">
      <circle cx="11" cy="11" r="7" opacity="0.15"/>
      <path d="M16.5 16.5L21 21" opacity="0.4"/>
      <circle cx="11" cy="11" r="7" stroke-width="1.5" stroke="currentColor" opacity="0.6"/>
      <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
    </svg>
    <!-- No services / empty data -->
    <svg v-else width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="empty-illustration">
      <path d="M20.5 7.5L12 3L3.5 7.5V16.5L12 21L20.5 16.5V7.5Z" opacity="0.15"/>
      <path d="M12 3L3.5 7.5V16.5L12 21L20.5 16.5V7.5L12 3Z" stroke-width="1.5" opacity="0.5"/>
      <path d="M12 12L3.5 7.5" opacity="0.3"/>
      <path d="M12 12V21" opacity="0.3"/>
      <path d="M12 12L20.5 7.5" opacity="0.3"/>
    </svg>
    <p>{{ message }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  type: { type: String, default: 'default' },
  message: { type: String, default: 'Belum ada data tersedia' }
})
</script>

<style scoped>
.empty-state-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 3rem 1rem;
  text-align: center;
  animation: emptyFadeIn 0.5s var(--ease-smooth, cubic-bezier(0.4,0,0.2,1)) both;
}

.empty-illustration {
  color: var(--accent-secondary, #14B8A6);
  opacity: 0.6;
  animation: emptyFloat 3s ease-in-out infinite;
}

.empty-state-wrapper p {
  color: var(--text-muted, #A8AEB8);
  font-size: var(--text-sm, 0.875rem);
  max-width: 280px;
  line-height: var(--leading-relaxed, 1.625);
}

@keyframes emptyFadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes emptyFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-6px); }
}
</style>
