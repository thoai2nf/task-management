<template>
  <v-container fluid class="register-container">
    <v-row justify="center" align="center" class="fill-height">
      <v-col cols="12" sm="8" md="6" lg="4">
        <div class="text-center mb-8">
          <h1 class="text-h3 font-weight-bold text-white">
            Task Management
          </h1>
          <p class="text-h6 text-white-50 mt-2">
            Tham gia để quản lý công việc hiệu quả
          </p>
        </div>

        <RegisterForm />
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import RegisterForm from '@/components/auth/RegisterForm.vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const authStore = useAuthStore()
const router = useRouter()

onMounted(() => {
  if (authStore.isLoggedIn) {
    router.push('/dashboard')
  }
})
</script>

<style scoped>
.register-container {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>