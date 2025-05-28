<template>
  <v-card
      class="task-card mb-3"
      :class="{ 'task-dragging': isDragging }"
      elevation="2"
      @mousedown="startDrag"
      @mouseup="endDrag"
  >
    <v-card-text class="pb-2">
      <div class="d-flex justify-space-between align-start mb-2">
        <h4 class="text-subtitle-1 font-weight-medium">{{ task.title }}</h4>

        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn
                icon="mdi-dots-vertical"
                v-bind="props"
                variant="text"
                size="small"
                density="compact"
            ></v-btn>
          </template>

          <v-list>
            <v-list-item @click="$emit('edit', task)">
              <template v-slot:prepend>
                <v-icon>mdi-pencil</v-icon>
              </template>
              <v-list-item-title>Chỉnh sửa</v-list-item-title>
            </v-list-item>

            <v-list-item @click="$emit('delete', task)">
              <template v-slot:prepend>
                <v-icon color="error">mdi-delete</v-icon>
              </template>
              <v-list-item-title class="text-error">Xóa</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <p v-if="task.description" class="text-body-2 text-medium-emphasis mb-2">
        {{ task.description }}
      </p>

      <div class="d-flex align-center justify-space-between">
        <div class="d-flex align-center">
          <v-chip
              :color="priorityColor"
              size="x-small"
              class="me-2"
          >
            {{ priorityText }}
          </v-chip>

          <v-chip
              :color="statusColor"
              size="x-small"
              variant="outlined"
          >
            {{ statusText }}
          </v-chip>
        </div>

        <div v-if="task.due_date" class="text-caption text-medium-emphasis">
          <v-icon size="small" class="me-1">mdi-calendar</v-icon>
          {{ formatDate(task.due_date) }}
        </div>
      </div>
    </v-card-text>

    <v-card-actions class="pt-0">
      <v-select
          v-model="localStatus"
          :items="statusItems"
          variant="outlined"
          density="compact"
          hide-details
          @update:model-value="updateStatus"
      ></v-select>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { formatDate } from '@/utils/helpers'

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'update-status'])

const isDragging = ref(false)
const localStatus = ref(props.task.status)

watch(() => props.task.status, (newStatus) => {
  localStatus.value = newStatus
})

const priorityColor = computed(() => {
  const colors = {
    low: 'green',
    medium: 'orange',
    high: 'red'
  }
  return colors[props.task.priority] || 'grey'
})

const priorityText = computed(() => {
  const texts = {
    low: 'Thấp',
    medium: 'Trung bình',
    high: 'Cao'
  }
  return texts[props.task.priority] || 'Không xác định'
})

const statusColor = computed(() => {
  const colors = {
    todo: 'grey',
    in_progress: 'blue',
    done: 'green'
  }
  return colors[props.task.status] || 'grey'
})

const statusText = computed(() => {
  const texts = {
    todo: 'Chờ làm',
    in_progress: 'Đang làm',
    done: 'Hoàn thành'
  }
  return texts[props.task.status] || 'Không xác định'
})

const statusItems = [
  { title: 'Chờ làm', value: 'todo' },
  { title: 'Đang làm', value: 'in_progress' },
  { title: 'Hoàn thành', value: 'done' }
]

const startDrag = () => {
  isDragging.value = true
}

const endDrag = () => {
  isDragging.value = false
}

const updateStatus = (newStatus) => {
  emit('update-status', { ...props.task, status: newStatus })
}
</script>

<style scoped>
.task-card {
  cursor: grab;
  transition: all 0.2s ease;
}

.task-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.task-dragging {
  cursor: grabbing;
  transform: rotate(5deg);
  opacity: 0.8;
}
</style>