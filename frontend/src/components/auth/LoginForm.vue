<template>
  <v-card class="mx-auto" max-width="400">
    <v-card-title class="text-center">
      <h2>Đăng nhập</h2>
    </v-card-title>

    <v-card-text>
      <v-form @submit.prevent="handleLogin">
        <v-text-field
            v-model="form.email"
            :error-messages="errors.email"
            label="Email"
            type="email"
            prepend-inner-icon="mdi-email"
            variant="outlined"
            required
        ></v-text-field>

        <v-text-field
            v-model="form.password"
            :error-messages="errors.password"
            label="Mật khẩu"
            :type="showPassword ? 'text' : 'password'"
            prepend-inner-icon="mdi-lock"
            :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append-inner="showPassword = !showPassword"
            variant="outlined"
            required
        ></v-text-field>

        <v-alert
            v-if="error"
            type="error"
            class="mb-4"
        >
          {{ error }}
        </v-alert>

        <v-btn
            type="submit"
            color="primary"
            :loading="loading"
            :disabled="loading"
            block
            class="mb-4"
        >
          Đăng nhập
        </v-btn>

        <div class="text-center">
          <router-link to="/register">
            Chưa có tài khoản? Đăng ký ngay
          </router-link>
        </div>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: [],
  password: []
})

const validateForm = () => {
  errors.email = []
  errors.password = []

  if (!form.email) {
    errors.email.push('Email là bắt buộc')
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email.push('Email không đúng định dạng')
  }

  if (!form.password) {
    errors.password.push('Mật khẩu là bắt buộc')
  }

  return errors.email.length === 0 && errors.password.length === 0
}

const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true
  error.value = ''

  try {
    await authStore.login(form)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Đăng nhập thất bại'
  } finally {
    loading.value = false
  }
}
</script>