<template>
  <div class="task-board">
    <v-row>
      <v-col
          v-for="column in columns"
          :key="column.status"
          cols="12"
          md="4"
      >
        <v-card class="task-column" elevation="1">
          <v-card-title class="text-center py-3">
            <v-chip
                :color="column.color"
                class="font-weight-bold"
            >
              {{ column.title }} ({{ getTasksByStatus(column.status).length }})
            </v-chip>
          </v-card-title>

          <v-card-text class="px-2">
            <draggable
                v-model="tasksByStatus[column.status]"
                :group="{ name: 'tasks', pull: true, put: true }"
                :animation="200"
                item-key="id"
                class="task-list"
                @change="handleDragChange"
            >
              <template #item="{ element }">
                <TaskCard
                    :task="element"
                    @edit="$emit('edit', $event)"
                    @delete="$emit('delete', $event)"
                    @update-status="handleStatusUpdate"
                />
              </template>
            </draggable>

            <div v-if="getTasksByStatus(column.status).length === 0" class="empty-column">
              <v-icon size="48" color="grey-lighten-2">mdi-drag</v-icon>
              <p class="text-caption text-grey-lighten-1 mt-2">
                Kéo thả nhiệm vụ vào đây
              </p>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import draggable from 'vuedraggable'
import TaskCard from './TaskCard.vue'

const props = defineProps({
  tasks: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['edit', 'delete', 'update-status', 'update-order'])

const columns = [
  {
    status: 'todo',
    title: 'Chờ làm',
    color: 'grey'
  },
  {
    status: 'in_progress',
    title: 'Đang làm',
    color: 'blue'
  },
  {
    status: 'done',
    title: 'Hoàn thành',
    color: 'green'
  }
]

const tasksByStatus = ref({
  todo: [],
  in_progress: [],
  done: []
})

watch(() => props.tasks, (newTasks) => {
  tasksByStatus.value = {
    todo: newTasks.filter(task => task.status === 'todo').sort((a, b) => a.order - b.order),
    in_progress: newTasks.filter(task => task.status === 'in_progress').sort((a, b) => a.order - b.order),
    done: newTasks.filter(task => task.status === 'done').sort((a, b) => a.order - b.order)
  }
}, { immediate: true })

const getTasksByStatus = (status) => {
  return tasksByStatus.value[status] || []
}

const handleDragChange = (evt) => {
  if (evt.added || evt.moved) {
    let affectedStatus = null
    let newOrder = []

    for (const [status, tasks] of Object.entries(tasksByStatus.value)) {
      if (tasks.some(task => task.id === evt.added?.element?.id || task.id === evt.moved?.element?.id)) {
        affectedStatus = status
        newOrder = tasks.map((task, index) => ({
          id: task.id,
          status: status,
          order: index + 1
        }))
        break
      }
    }

    if (evt.added) {
      const task = evt.added.element
      if (task.status !== affectedStatus) {
        handleStatusUpdate({ ...task, status: affectedStatus })
      }
    }

    emit('update-order', newOrder)
  }
}

const handleStatusUpdate = (updatedTask) => {
  emit('update-status', updatedTask)
}
</script>

<style scoped>
.task-board {
  height: calc(100vh - 200px);
}

.task-column {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.task-list {
  min-height: 200px;
  flex: 1;
}

.empty-column {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px;
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  margin: 16px 0;
}

.sortable-ghost {
  opacity: 0.5;
}

.sortable-chosen {
  transform: rotate(5deg);
}
</style>