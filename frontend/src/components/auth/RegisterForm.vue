<template>
  <v-card class="mx-auto" max-width="400">
    <v-card-title class="text-center">
      <h2>Đăng ký</h2>
    </v-card-title>

    <v-card-text>
      <v-form @submit.prevent="handleRegister">
        <v-text-field
            v-model="form.name"
            :error-messages="errors.name"
            label="Họ tên"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            required
        ></v-text-field>

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

        <v-text-field
            v-model="form.password_confirmation"
            :error-messages="errors.password_confirmation"
            label="Xác nhận mật khẩu"
            :type="showPasswordConfirm ? 'text' : 'password'"
            prepend-inner-icon="mdi-lock-check"
            :append-inner-icon="showPasswordConfirm ? 'mdi-eye' : 'mdi-eye-off'"
            @click:append-inner="showPasswordConfirm = !showPasswordConfirm"
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
          Đăng ký
        </v-btn>

        <div class="text-center">
          <router-link to="/login">
            Đã có tài khoản? Đăng nhập ngay
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
const showPasswordConfirm = ref(false)
const loading = ref(false)
const error = ref('')

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: [],
  email: [],
  password: [],
  password_confirmation: []
})

const validateForm = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = []
  })

  if (!form.name) {
    errors.name.push('Họ tên là bắt buộc')
  }

  if (!form.email) {
    errors.email.push('Email là bắt buộc')
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email.push('Email không đúng định dạng')
  }

  if (!form.password) {
    errors.password.push('Mật khẩu là bắt buộc')
  } else if (form.password.length < 8) {
    errors.password.push('Mật khẩu phải có ít nhất 8 ký tự')
  }

  if (!form.password_confirmation) {
    errors.password_confirmation.push('Xác nhận mật khẩu là bắt buộc')
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation.push('Xác nhận mật khẩu không khớp')
  }

  return Object.values(errors).every(errorArray => errorArray.length === 0)
}

const handleRegister = async () => {
  if (!validateForm()) return

  loading.value = true
  error.value = ''

  try {
    await authStore.register(form)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Đăng ký thất bại'
  } finally {
    loading.value = false
  }
}
</script>