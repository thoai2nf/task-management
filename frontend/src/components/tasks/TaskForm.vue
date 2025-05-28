<template>
  <v-dialog
      v-model="dialog"
      max-width="600px"
      persistent
  >
    <v-card>
      <v-card-title>
        <span class="text-h5">{{ isEdit ? 'Chỉnh sửa nhiệm vụ' : 'Tạo nhiệm vụ mới' }}</span>
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

          <v-row>
            <v-col cols="12" md="6">
              <v-select
                  v-model="form.priority"
                  :items="priorityItems"
                  label="Độ ưu tiên"
                  variant="outlined"
              ></v-select>
            </v-col>

            <v-col cols="12" md="6">
              <v-select
                  v-model="form.status"
                  :items="statusItems"
                  label="Trạng thái"
                  variant="outlined"
              ></v-select>
            </v-col>
          </v-row>

          <v-text-field
              v-model="form.due_date"
              :error-messages="errors.due_date"
              label="Ngày hết hạn"
              type="date"
              variant="outlined"
          ></v-text-field>
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
  task: {
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

const isEdit = computed(() => !!props.task)

const form = reactive({
  title: '',
  description: '',
  priority: 'medium',
  status: 'todo',
  due_date: ''
})

const errors = reactive({
  title: [],
  description: [],
  due_date: []
})

const priorityItems = [
  { title: 'Thấp', value: 'low' },
  { title: 'Trung bình', value: 'medium' },
  { title: 'Cao', value: 'high' }
]

const statusItems = [
  { title: 'Chờ làm', value: 'todo' },
  { title: 'Đang làm', value: 'in_progress' },
  { title: 'Hoàn thành', value: 'done' }
]

// 🔧 MOVED: Define resetForm BEFORE watch
const resetForm = () => {
  form.title = ''
  form.description = ''
  form.priority = 'medium'
  form.status = 'todo'
  form.due_date = ''
  Object.keys(errors).forEach(key => {
    errors[key] = []
  })
}

// Watch for task changes to populate form
watch(() => props.task, (newTask) => {
  if (newTask) {
    form.title = newTask.title || ''
    form.description = newTask.description || ''
    form.priority = newTask.priority || 'medium'
    form.status = newTask.status || 'todo'
    form.due_date = newTask.due_date || ''
  } else {
    resetForm() // Now this is accessible
  }
}, { immediate: true })

const validateForm = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = []
  })

  if (!form.title.trim()) {
    errors.title.push('Tiêu đề là bắt buộc')
  }

  if (form.due_date && new Date(form.due_date) < new Date().setHours(0,0,0,0)) {
    errors.due_date.push('Ngày hết hạn phải từ hôm nay trở đi')
  }

  return Object.values(errors).every(errorArray => errorArray.length === 0)
}

const handleSubmit = () => {
  if (!validateForm()) return

  const submitData = { ...form }
  if (!submitData.due_date) {
    delete submitData.due_date
  }

  emit('submit', submitData)
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}
</script>