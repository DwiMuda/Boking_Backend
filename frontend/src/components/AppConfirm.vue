<template>
  <AppModal :open="open" title="Konfirmasi" @close="$emit('cancel')">
    <p class="confirm-message">{{ message }}</p>
    <template #footer>
      <button class="btn btn-ghost" @click="$emit('cancel')">Batal</button>
      <button class="btn btn-danger" @click="$emit('confirm')">Ya, {{ actionLabel }}</button>
    </template>
  </AppModal>
</template>

<script setup>
import AppModal from './AppModal.vue'

defineProps({
  open: Boolean,
  message: { type: String, default: 'Yakin ingin melanjutkan?' },
  actionLabel: { type: String, default: 'Lanjutkan' }
})

defineEmits(['confirm', 'cancel'])
</script>

<style scoped>
.confirm-message {
  color: var(--text-secondary);
  font-size: var(--text-sm);
  line-height: var(--leading-relaxed);
}
</style>
