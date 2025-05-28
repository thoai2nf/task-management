import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

export function useAuth() {
    const authStore = useAuthStore()
    const router = useRouter()

    const user = computed(() => authStore.user)
    const isLoggedIn = computed(() => authStore.isLoggedIn)
    const loading = computed(() => authStore.loading)

    const login = async (credentials) => {
        try {
            await authStore.login(credentials)
            router.push('/dashboard')
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Đăng nhập thất bại'
            }
        }
    }

    const register = async (userData) => {
        try {
            await authStore.register(userData)
            router.push('/dashboard')
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Đăng ký thất bại'
            }
        }
    }

    const logout = async () => {
        try {
            await authStore.logout()
            router.push('/login')
            return { success: true }
        } catch (error) {
            return {
                success: false,
                error: error.response?.data?.message || 'Đăng xuất thất bại'
            }
        }
    }

    const refreshToken = async () => {
        try {
            await authStore.refresh()
            return { success: true }
        } catch (error) {
            await logout()
            return { success: false }
        }
    }

    return {
        user,
        isLoggedIn,
        loading,
        login,
        register,
        logout,
        refreshToken
    }
}