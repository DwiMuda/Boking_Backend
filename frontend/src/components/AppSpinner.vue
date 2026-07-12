<template>
  <span v-if="variant === 'ring'" :class="['spinner-ring', size]" role="status" :aria-label="label">
    <span class="sr-only">{{ label }}</span>
  </span>
  <span v-else :class="['spinner-dots', size]" role="status" :aria-label="label">
    <span></span><span></span><span></span>
    <span class="sr-only">{{ label }}</span>
  </span>
</template>

<script setup>
defineProps({
  variant: { type: String, default: 'ring' },
  size: { type: String, default: 'md' },
  label: { type: String, default: 'Memuat...' }
})
</script>

<style scoped>
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  margin: -1px;
  padding: 0;
  overflow: hidden;
  clip: rect(0,0,0,0);
  border: 0;
}
</style>
