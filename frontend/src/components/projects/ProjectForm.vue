<template>
  <v-dialog
      v-model="dialog"
      max-width="600px"
      persistent
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ isEdit ? 'Chỉnh sửa dự án' : 'Tạo dự án mới' }}</span>
      </v-card-title>

      <v-card-text>
        <v-form ref="formRef" @submit.prevent="handleSubmit">
          <v-text-field
              v-model="form.title"
              :error-messages="errors.title"
              label="Tiêu đề *"
              variant="outlined"
              required
              autofocus
          ></v-text-field>

          <v-textarea
              v-model="form.description"
              :error-messages="errors.description"
              label="Mô tả"
              variant="outlined"
              rows="3"
          ></v-textarea>

          <v-select
              v-model="form.status"
              :items="statusItems"
              label="Trạng thái"
              variant="outlined"
          ></v-select>
        </v-form>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click="handleCancel">Hủy</v-btn>
        <v-btn
            color="primary"
            :loading="loading"
            @click="handleSubmit"
        >
          {{ isEdit ? 'Cập nhật' : 'Tạo' }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  project: {
    type: Object,
    default: null
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'submit', 'cancel'])

const formRef = ref()
const dialog = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const isEdit = computed(() => !!props.project)

const form = reactive({
  title: '',
  description: '',
  status: 'active'
})

const errors = reactive({
  title: [],
  description: []
})

const statusItems = [
  { title: 'Hoạt động', value: 'active' },
  { title: 'Không hoạt động', value: 'inactive' }
]

// 🔧 MOVED: Define resetForm BEFORE watch
const resetForm = () => {
  form.title = ''
  form.description = ''
  form.status = 'active'
  errors.title = []
  errors.description = []
}

// Watch for project changes to populate form
watch(() => props.project, (newProject) => {
  if (newProject) {
    form.title = newProject.title || ''
    form.description = newProject.description || ''
    form.status = newProject.status || 'active'
  } else {
    resetForm() // Now this is accessible
  }
}, { immediate: true })

const validateForm = () => {
  errors.title = []
  errors.description = []

  if (!form.title.trim()) {
    errors.title.push('Tiêu đề là bắt buộc')
  }

  return errors.title.length === 0
}

const handleSubmit = () => {
  if (!validateForm()) return

  emit('submit', { ...form })
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}
</script>