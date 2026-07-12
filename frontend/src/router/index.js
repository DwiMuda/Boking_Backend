import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Services.vue')
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/Login.vue')
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('../views/Register.vue')
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('../views/Profile.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/services',
    name: 'Services',
    component: () => import('../views/Services.vue')
  },
  {
    path: '/services/:id',
    name: 'ServiceDetail',
    component: () => import('../views/ServiceDetail.vue')
  },
  {
    path: '/booking/:serviceId',
    name: 'BookingForm',
    component: () => import('../views/BookingForm.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/booking/:id/confirmation',
    name: 'Confirmation',
    component: () => import('../views/Confirmation.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/my-bookings',
    name: 'MyBookings',
    component: () => import('../views/MyBookings.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    name: 'AdminDashboard',
    component: () => import('../views/admin/Dashboard.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/bookings',
    name: 'AdminBookings',
    component: () => import('../views/admin/ManageBookings.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/services',
    name: 'AdminServices',
    component: () => import('../views/admin/ManageServices.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/reports',
    name: 'AdminReports',
    component: () => import('../views/admin/Reports.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  if (to.meta.requiresAuth && !auth.isLoggedIn) {
    next({ name: 'Login' })
  } else if (to.meta.requiresAdmin && !auth.isAdmin) {
    next({ name: 'Home' })
  } else {
    next()
  }
})

export default router
