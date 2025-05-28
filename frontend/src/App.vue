<template>
  <v-app>
    <template v-if="authStore.isLoggedIn">
      <AppHeader @toggle-drawer="drawerOpen = !drawerOpen" />
      <AppSidebar v-model="drawerOpen" />

      <v-main>
        <router-view />
      </v-main>
    </template>

    <template v-else>
      <router-view />
    </template>
  </v-app>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppHeader from '@/components/common/AppHeader.vue'
import AppSidebar from '@/components/common/AppSidebar.vue'

const authStore = useAuthStore()
const drawerOpen = ref(false)

onMounted(async () => {
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (error) {
      console.error('Failed to fetch user:', error)
    }
  }
})
</script>

<style>
body {
  font-family: 'Inter', sans-serif;
}
</style>