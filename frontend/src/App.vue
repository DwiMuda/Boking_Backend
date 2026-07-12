<template>
  <div id="app">
    <NavbarFuturistic />

    <router-view v-slot="{ Component, route }">
      <transition name="page" mode="out-in">
        <component :is="Component" :key="route.path" />
      </transition>
    </router-view>

    <AppToast />
  </div>
</template>

<script setup>
import NavbarFuturistic from './components/NavbarFuturistic.vue'
</script>

<style scoped>
.page-enter-active,
.page-leave-active {
  transition: all 0.25s ease;
}
.page-enter-from {
  opacity: 0;
  transform: translateY(12px);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
