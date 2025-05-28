<template>
  <v-app-bar app color="primary" dark>
    <v-app-bar-nav-icon @click="$emit('toggle-drawer')"></v-app-bar-nav-icon>

    <v-toolbar-title>Task Management</v-toolbar-title>

    <v-spacer></v-spacer>

    <v-menu offset-y>
      <template v-slot:activator="{ props }">
        <v-btn icon v-bind="props">
          <v-avatar size="32">
            <v-icon>mdi-account-circle</v-icon>
          </v-avatar>
        </v-btn>
      </template>

      <v-list>
        <v-list-item>
          <v-list-item-title>{{ authStore.user?.name }}</v-list-item-title>
          <v-list-item-subtitle>{{ authStore.user?.email }}</v-list-item-subtitle>
        </v-list-item>

        <v-divider></v-divider>

        <v-list-item @click="logout">
          <template v-slot:prepend>
            <v-icon>mdi-logout</v-icon>
          </template>
          <v-list-item-title>Đăng xuất</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

defineEmits(['toggle-drawer'])

const logout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
  }
}
</script>