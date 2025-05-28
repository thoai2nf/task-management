<template>
  <v-snackbar
      v-model="show"
      :timeout="timeout"
      :color="color"
      :location="location"
  >
    {{ message }}
    <template v-slot:actions>
      <v-btn
          variant="text"
          @click="show = false"
      >
        Đóng
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  message: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  timeout: {
    type: Number,
    default: 5000
  },
  location: {
    type: String,
    default: 'top'
  }
})

const emit = defineEmits(['update:modelValue'])

const show = ref(props.modelValue)

const colorMap = {
  success: 'success',
  error: 'error',
  warning: 'warning',
  info: 'info'
}

const color = computed(() => colorMap[props.type] || 'info')

watch(() => props.modelValue, (newVal) => {
  show.value = newVal
})

watch(show, (newVal) => {
  emit('update:modelValue', newVal)
})
</script>