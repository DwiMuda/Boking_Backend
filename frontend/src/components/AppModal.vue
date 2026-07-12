<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="open" class="modal-overlay" @click.self="handleBackdrop" @keydown.escape="handleEscape" tabindex="-1" ref="overlayRef">
        <div class="modal-card" role="dialog" :aria-label="title || 'Dialog'">
          <header class="modal-header">
            <h3>{{ title }}</h3>
            <button class="modal-close" @click="close" aria-label="Tutup">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </header>
          <div class="modal-body">
            <slot />
          </div>
          <footer v-if="$slots.footer" class="modal-footer">
            <slot name="footer" />
          </footer>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch, onMounted, onUnmounted, ref } from 'vue'

const props = defineProps({
  open: Boolean,
  title: { type: String, default: '' }
})

const emit = defineEmits(['update:open', 'close'])

const overlayRef = ref(null)

const close = () => {
  emit('update:open', false)
  emit('close')
}

const handleBackdrop = () => close()

const handleEscape = (e) => {
  if (e.key === 'Escape') close()
}

watch(() => props.open, (val) => {
  if (val) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.body.style.overflow = ''
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  z-index: var(--z-modal-backdrop, 400);
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-card {
  background: var(--bg-elevated);
  border: 1px solid var(--border-default);
  border-radius: var(--radius-lg);
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-subtle);
}

.modal-header h3 {
  font-size: var(--text-lg, 1.125rem);
  font-weight: var(--weight-bold, 700);
}

.modal-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: var(--radius-sm);
  background: transparent;
  border: none;
  color: var(--text-muted);
  cursor: pointer;
  transition: all var(--duration-fast) var(--ease-smooth);
}

.modal-close:hover {
  background: var(--bg-hover);
  color: var(--text-primary);
  transform: rotate(90deg);
}

.modal-body {
  padding: 1.25rem 1.5rem;
}

.modal-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  padding: 1rem 1.5rem;
  border-top: 1px solid var(--border-subtle);
}

/* Transition */
.modal-enter-active { transition: all 0.25s var(--ease-smooth); }
.modal-leave-active { transition: all 0.2s var(--ease-smooth); }
.modal-enter-from { opacity: 0; }
.modal-enter-from .modal-card { transform: scale(0.95) translateY(10px); opacity: 0; }
.modal-leave-to { opacity: 0; }
.modal-leave-to .modal-card { transform: scale(0.95) translateY(10px); opacity: 0; }
.modal-enter-to .modal-card,
.modal-leave-from .modal-card {
  transition: all 0.25s var(--ease-smooth);
}
</style>
