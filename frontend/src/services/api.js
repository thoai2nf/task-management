import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import router from '@/router'

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    },
})

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const authStore = useAuthStore()
        if (authStore.token) {
            config.headers.Authorization = `Bearer ${authStore.token}`
        }
        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

// Response interceptor
api.interceptors.response.use(
    (response) => response,
    async (error) => {
        const authStore = useAuthStore()

        if (error.response?.status === 401 && authStore.token) {
            try {
                await authStore.refresh()
                // Retry original request
                const originalRequest = error.config
                originalRequest.headers.Authorization = `Bearer ${authStore.token}`
                return api.request(originalRequest)
            } catch (refreshError) {
                authStore.logout()
                router.push('/login')
            }
        }

        return Promise.reject(error)
    }
)

export default api