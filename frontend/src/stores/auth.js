import { defineStore } from 'pinia'
import { authService } from '@/services/authService'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        isAuthenticated: false,
        loading: false
    }),

    getters: {
        isLoggedIn: (state) => !!state.token && !!state.user,
    },

    actions: {
        async login(credentials) {
            this.loading = true
            try {
                const response = await authService.login(credentials)

                if (response.success) {
                    this.token = response.data.token
                    this.user = response.data.user
                    this.isAuthenticated = true

                    localStorage.setItem('token', this.token)
                    localStorage.setItem('user', JSON.stringify(this.user))

                    return response
                }
            } catch (error) {
                this.logout()
                throw error
            } finally {
                this.loading = false
            }
        },

        async register(userData) {
            this.loading = true
            try {
                const response = await authService.register(userData)

                if (response.success) {
                    this.token = response.data.token
                    this.user = response.data.user
                    this.isAuthenticated = true

                    localStorage.setItem('token', this.token)
                    localStorage.setItem('user', JSON.stringify(this.user))

                    return response
                }
            } catch (error) {
                this.logout()
                throw error
            } finally {
                this.loading = false
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await authService.logout()
                }
            } catch (error) {
                console.error('Logout error:', error)
            } finally {
                this.user = null
                this.token = null
                this.isAuthenticated = false

                localStorage.removeItem('token')
                localStorage.removeItem('user')
            }
        },

        async refresh() {
            try {
                const response = await authService.refresh()

                if (response.success) {
                    this.token = response.data.token
                    this.user = response.data.user

                    localStorage.setItem('token', this.token)
                    localStorage.setItem('user', JSON.stringify(this.user))

                    return response
                }
            } catch (error) {
                this.logout()
                throw error
            }
        },

        async fetchUser() {
            if (!this.token) return

            try {
                const response = await authService.me()

                if (response.success) {
                    this.user = response.data
                    this.isAuthenticated = true
                    localStorage.setItem('user', JSON.stringify(this.user))
                }
            } catch (error) {
                this.logout()
                throw error
            }
        },

        initializeAuth() {
            const token = localStorage.getItem('token')
            const user = localStorage.getItem('user')

            if (token && user) {
                this.token = token
                this.user = JSON.parse(user)
                this.isAuthenticated = true
            }
        }
    }
})