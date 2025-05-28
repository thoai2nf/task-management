import { createApp } from 'vue'
import { createPinia } from 'pinia'
import vuetify from './plugins/vuetify'
import router from './router'
import { useAuthStore } from './stores/auth'

import App from './App.vue'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(vuetify)

// Initialize auth store
const authStore = useAuthStore()
authStore.initializeAuth()

app.mount('#app')