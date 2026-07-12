<template>
  <div id="toast-container"></div>
</template>

<style scoped>
:global(#toast-container) {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: var(--z-toast, 600);
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  pointer-events: none;
  max-width: 400px;
  width: calc(100% - 2rem);
}
</style>
