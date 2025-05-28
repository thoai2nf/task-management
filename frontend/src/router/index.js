import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const LoginView = () => import('@/views/auth/LoginView.vue')
const RegisterView = () => import('@/views/auth/RegisterView.vue')
const DashboardView = () => import('@/views/dashboard/DashboardView.vue')
const ProjectsView = () => import('@/views/projects/ProjectsView.vue')
const ProjectDetailView = () => import('@/views/projects/ProjectDetailView.vue')

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/dashboard'
    },
    {
      path: '/login',
      name: 'Login',
      component: LoginView,
      meta: {
        requiresGuest: true,
        title: 'Đăng nhập'
      }
    },
    {
      path: '/register',
      name: 'Register',
      component: RegisterView,
      meta: {
        requiresGuest: true,
        title: 'Đăng ký'
      }
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: DashboardView,
      meta: {
        requiresAuth: true,
        title: 'Dashboard'
      }
    },
    {
      path: '/projects',
      name: 'Projects',
      component: ProjectsView,
      meta: {
        requiresAuth: true,
        title: 'Dự án'
      }
    },
    {
      path: '/projects/:id',
      name: 'ProjectDetail',
      component: ProjectDetailView,
      meta: {
        requiresAuth: true,
        title: 'Chi tiết dự án'
      }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      redirect: '/dashboard'
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (!authStore.user && localStorage.getItem('token')) {
    authStore.initializeAuth()
  }

  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/login')
    return
  }

  if (to.meta.requiresGuest && authStore.isLoggedIn) {
    next('/dashboard')
    return
  }

  if (to.meta.title) {
    document.title = `${to.meta.title} - Task Management`
  }

  next()
})

export default router