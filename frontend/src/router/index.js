import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/about',
    name: 'About',
    component: () => import('../views/About.vue')
  },
  {
    path: '/gallery',
    name: 'Gallery',
    component: () => import('../views/Gallery.vue')
  },
  {
    path: '/testimonials',
    name: 'Testimonials',
    component: () => import('../views/Testimonials.vue')
  },
  {
    path: '/offers',
    name: 'Offers',
    component: () => import('../views/Offers.vue')
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
    path: '/rooms',
    name: 'Rooms',
    component: () => import('../views/Rooms.vue')
  },
  {
    path: '/rooms/:id',
    name: 'RoomDetail',
    component: () => import('../views/RoomDetail.vue')
  },
  {
    path: '/booking-room/:roomTypeId',
    name: 'RoomBooking',
    component: () => import('../views/RoomBooking.vue'),
    meta: { requiresAuth: true }
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
    path: '/admin/rooms',
    name: 'AdminRoomTypes',
    component: () => import('../views/admin/RoomTypes.vue'),
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
