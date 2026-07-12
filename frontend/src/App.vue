<template>
  <div id="app">
    <nav class="navbar">
      <router-link to="/" class="navbar-brand">Booking System</router-link>
      <div class="navbar-menu">
        <router-link to="/services">Layanan</router-link>
        <router-link v-if="auth.isLoggedIn" to="/my-bookings">Booking Saya</router-link>
        <router-link v-if="auth.isAdmin" to="/admin">Dashboard</router-link>
        <router-link v-if="auth.isAdmin" to="/admin/bookings">Booking</router-link>
        <router-link v-if="auth.isAdmin" to="/admin/services">Layanan</router-link>
        <router-link v-if="auth.isAdmin" to="/admin/reports">Laporan</router-link>
        <template v-if="auth.isLoggedIn">
          <router-link to="/profile">{{ auth.userName }}</router-link>
          <button class="btn-logout" @click="handleLogout">Logout</button>
        </template>
        <template v-else>
          <router-link to="/login">Login</router-link>
          <router-link to="/register">Daftar</router-link>
        </template>
      </div>
    </nav>
    <div class="container">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

function handleLogout() {
  auth.logout()
  router.push('/login')
}
</script>
