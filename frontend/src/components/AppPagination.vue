<template>
  <div v-if="totalPages > 1" class="pagination">
    <button :disabled="modelValue <= 1" class="btn btn-ghost btn-sm" @click="goTo(modelValue - 1)" aria-label="Halaman sebelumnya">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>

    <template v-for="p in visiblePages" :key="p">
      <span v-if="p === '...'" class="pagination-dots">...</span>
      <button v-else :class="['btn btn-sm', p === modelValue ? 'btn-primary' : 'btn-ghost']" @click="goTo(p)">{{ p }}</button>
    </template>

    <button :disabled="modelValue >= totalPages" class="btn btn-ghost btn-sm" @click="goTo(modelValue + 1)" aria-label="Halaman selanjutnya">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: { type: Number, default: 1 },
  totalPages: { type: Number, default: 1 }
})

const emit = defineEmits(['update:modelValue'])

const visiblePages = computed(() => {
  const pages = []
  const current = props.modelValue
  const last = props.totalPages
  if (last <= 5) {
    for (let i = 1; i <= last; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) pages.push(i)
    if (current < last - 2) pages.push('...')
    pages.push(last)
  }
  return pages
})

function goTo(p) {
  emit('update:modelValue', p)
}
</script>
