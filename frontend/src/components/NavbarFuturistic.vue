<template>
  <nav class="navbar-futuristic" :class="{ 'navbar-hidden': hideNav }">
    <div class="nf-container">
      <router-link to="/" class="nf-brand" @click="closeNav">
        <div class="nf-brand-icon">
          <PalmtreeIcon :size="20" />
        </div>
        <span class="nf-brand-text">Tropical</span>
      </router-link>

      <div class="nf-menu" :class="{ 'nf-menu-open': mobileOpen }">
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nf-link"
          :class="{ active: isActive(item.path) }"
          @click="closeNav"
        >
          <component :is="item.icon" :size="16" />
          <span>{{ item.label }}</span>
        </router-link>
      </div>

      <div class="nf-actions">
        <template v-if="auth.isLoggedIn">
          <div class="nf-user" @click="userOpen = !userOpen">
            <button class="nf-avatar">
              {{ auth.userName?.charAt(0) || 'U' }}
            </button>
            <Transition name="nf-drop">
              <div v-if="userOpen" class="nf-dropdown">
                <router-link to="/profile" class="nf-drop-item" @click="closeAll">
                  <UserIcon :size="14" /> Profil
                </router-link>
                <router-link to="/my-bookings" class="nf-drop-item" @click="closeAll">
                  <CalendarCheckIcon :size="14" /> Booking Saya
                </router-link>
                <div v-if="auth.isAdmin" class="nf-drop-divider"></div>
                <router-link v-if="auth.isAdmin" to="/admin" class="nf-drop-item" @click="closeAll">
                  <LayoutDashboardIcon :size="14" /> Admin
                </router-link>
                <div class="nf-drop-divider"></div>
                <button class="nf-drop-item nf-drop-danger" @click="handleLogout">
                  <LogOutIcon :size="14" /> Logout
                </button>
              </div>
            </Transition>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="nf-btn-login">Masuk</router-link>
          <router-link to="/register" class="nf-btn-register">Daftar</router-link>
        </template>

        <button class="nf-hamburger" :class="{ active: mobileOpen }" @click="mobileOpen = !mobileOpen" aria-label="Menu">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import {
  HomeIcon, KeyIcon, PalmtreeIcon, CompassIcon, ImageIcon,
  UserIcon, LogOutIcon, CalendarCheckIcon, LayoutDashboardIcon
} from '@lucide/vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const mobileOpen = ref(false)
const userOpen = ref(false)
const hideNav = ref(false)

const menuItems = [
  { path: '/', label: 'Beranda', icon: HomeIcon },
  { path: '/rooms', label: 'Kamar', icon: KeyIcon },
  { path: '/services', label: 'Layanan', icon: PalmtreeIcon },
  { path: '/gallery', label: 'Galeri', icon: ImageIcon },
  { path: '/about', label: 'Tentang', icon: CompassIcon },
]

function isActive(path) {
  if (path === '/') return route.path === '/'
  return route.path.startsWith(path)
}

function closeNav() {
  mobileOpen.value = false
  userOpen.value = false
}

function closeAll() {
  closeNav()
}

function handleLogout() {
  auth.logout()
  closeAll()
  router.push('/login')
}

let scrollY = 0
onMounted(() => {
  scrollY = window.scrollY
  window.addEventListener('scroll', () => {
    hideNav.value = window.scrollY > scrollY && window.scrollY > 100
    scrollY = window.scrollY
  }, { passive: true })
})
</script>

<style scoped>
.navbar-futuristic {
  position: fixed;
  top: 1rem;
  left: 50%;
  transform: translateX(-50%);
  z-index: 300;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar-hidden {
  transform: translateX(-50%) translateY(-120%);
  opacity: 0;
}

.nf-container {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.45rem 0.6rem;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(0, 0, 0, 0.05);
  border-radius: 9999px;
  box-shadow:
    0 8px 40px rgba(0, 0, 0, 0.08),
    0 0 80px rgba(45, 90, 69, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.5);
}

.nf-brand {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.35rem 1rem 0.35rem 0.5rem;
  text-decoration: none;
  color: var(--text-primary);
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-weight: 700;
  font-size: 0.9rem;
  border-right: 1px solid rgba(0, 0, 0, 0.06);
  margin-right: 0.25rem;
}

.nf-brand-icon {
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  border-radius: 8px;
  color: white;
}

.nf-menu {
  display: flex;
  align-items: center;
  gap: 0.15rem;
}

.nf-link {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.45rem 1rem;
  border-radius: 9999px;
  text-decoration: none;
  color: var(--text-secondary);
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  white-space: nowrap;
}

.nf-link:hover {
  color: var(--text-primary);
  background: rgba(0, 0, 0, 0.04);
}

.nf-link.active {
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  color: white;
  font-weight: 600;
  box-shadow:
    0 0 16px rgba(45, 90, 69, 0.35),
    0 0 40px rgba(92, 138, 107, 0.12);
}

.nf-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-left: 0.25rem;
  padding-left: 0.75rem;
  border-left: 1px solid rgba(0, 0, 0, 0.06);
}

.nf-user {
  position: relative;
}

.nf-avatar {
  width: 32px;
  height: 32px;
  border-radius: 9999px;
  background: linear-gradient(135deg, #2D5A45, #5C8A6B);
  border: none;
  color: white;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-weight: 600;
  font-size: 0.8rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 12px rgba(45, 90, 69, 0.2);
}

.nf-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 180px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 16px;
  padding: 0.4rem;
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
}

.nf-drop-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.55rem 0.75rem;
  border-radius: 10px;
  text-decoration: none;
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  font-weight: 500;
  cursor: pointer;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  transition: all 0.15s ease;
}

.nf-drop-item:hover {
  background: rgba(0, 0, 0, 0.04);
  color: var(--text-primary);
}

.nf-drop-danger:hover {
  color: #EF4444;
  background: rgba(239, 68, 68, 0.08);
}

.nf-drop-divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.06);
  margin: 0.25rem 0.5rem;
}

.nf-btn-login {
  padding: 0.4rem 1rem;
  border-radius: 9999px;
  text-decoration: none;
  color: var(--text-secondary);
  font-size: 0.8rem;
  font-weight: 500;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  transition: all 0.15s ease;
}

.nf-btn-login:hover {
  color: var(--text-primary);
}

.nf-btn-register {
  padding: 0.4rem 1.1rem;
  border-radius: 9999px;
  text-decoration: none;
  background: linear-gradient(135deg, #C9A15C, #C1694F);
  color: white;
  font-size: 0.8rem;
  font-weight: 600;
  font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
  box-shadow: 0 0 16px rgba(201, 161, 92, 0.25);
  transition: all 0.2s ease;
}

.nf-btn-register:hover {
  box-shadow: 0 0 24px rgba(201, 161, 92, 0.4);
  transform: translateY(-1px);
}

.nf-hamburger {
  display: none;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
}

.nf-hamburger span {
  display: block;
  width: 18px;
  height: 2px;
  background: var(--text-primary);
  border-radius: 2px;
  transition: all 0.25s ease;
}

.nf-hamburger.active span:nth-child(1) {
  transform: rotate(45deg) translate(4px, 4px);
}
.nf-hamburger.active span:nth-child(2) {
  opacity: 0;
}
.nf-hamburger.active span:nth-child(3) {
  transform: rotate(-45deg) translate(4px, -4px);
}

.nf-drop-enter-active, .nf-drop-leave-active {
  transition: all 0.2s ease;
}
.nf-drop-enter-from, .nf-drop-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@media (max-width: 768px) {
  .navbar-futuristic {
    top: 0.75rem;
    left: 0.75rem;
    right: 0.75rem;
    transform: none;
  }

  .navbar-hidden {
    transform: translateY(-120%);
  }

  .nf-container {
    border-radius: 20px;
    padding: 0.4rem 0.5rem;
    flex-wrap: wrap;
  }

  .nf-menu {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    flex-direction: column;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 20px;
    padding: 0.5rem;
    gap: 0.15rem;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
  }

  .nf-menu-open {
    display: flex;
  }

  .nf-link {
    width: 100%;
    padding: 0.65rem 1rem;
    font-size: 0.9rem;
  }

  .nf-actions {
    margin-left: auto;
  }

  .nf-hamburger {
    display: flex;
  }

  .nf-brand-text {
    display: none;
  }
}
</style>
