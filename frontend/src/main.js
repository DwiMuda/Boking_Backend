import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'

import { installToast } from './utils/toast'
import AppSpinner from './components/AppSpinner.vue'
import AppSkeleton from './components/AppSkeleton.vue'
import AppModal from './components/AppModal.vue'
import AppEmptyState from './components/AppEmptyState.vue'
import AppToast from './components/AppToast.vue'
import AppPagination from './components/AppPagination.vue'
import AppConfirm from './components/AppConfirm.vue'

const app = createApp(App)
app.use(createPinia())
app.use(router)
installToast(app)

app.component('AppSpinner', AppSpinner)
app.component('AppSkeleton', AppSkeleton)
app.component('AppModal', AppModal)
app.component('AppEmptyState', AppEmptyState)
app.component('AppToast', AppToast)
app.component('AppPagination', AppPagination)
app.component('AppConfirm', AppConfirm)

app.directive('observe', {
  mounted(el) {
    const observer = new IntersectionObserver(([entry]) => {
      if (entry.isIntersecting) {
        el.classList.add('v-observe--visible')
        observer.unobserve(el)
      }
    }, { threshold: 0.1 })
    observer.observe(el)
    el._observer = observer
  },
  unmounted(el) {
    el._observer?.unobserve(el)
  }
})

app.mount('#app')
