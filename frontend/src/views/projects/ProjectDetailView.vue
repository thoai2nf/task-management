<template>
  <v-container fluid>
    <div v-if="loading" class="text-center py-8">
      <LoadingSpinner text="Đang tải thông tin dự án..." />
    </div>

    <div v-else-if="!project" class="text-center py-8">
      <v-icon size="64" color="grey-lighten-1">mdi-folder-off-outline</v-icon>
      <p class="text-h6 text-grey-lighten-1 mt-4">Dự án không tồn tại</p>
      <v-btn color="primary" to="/projects">
        Quay lại danh sách dự án
      </v-btn>
    </div>

    <div v-else>
      <!-- Project Header -->
      <div class="d-flex justify-space-between align-center mb-6">
        <div>
          <div class="d-flex align-center mb-2">
            <v-btn
                icon="mdi-arrow-left"
                variant="text"
                @click="$router.go(-1)"
                class="me-3"
            ></v-btn>
            <h1 class="text-h4 font-weight-bold">{{ project.title }}</h1>
            <v-chip
                :color="project.status === 'active' ? 'success' : 'grey'"
                class="ml-3"
            >
              {{ project.status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
            </v-chip>
          </div>

          <p v-if="project.description" class="text-subtitle-1 text-medium-emphasis">
            {{ project.description }}
          </p>
        </div>

        <div class="d-flex gap-2">
          <v-btn
              color="primary"
              variant="outlined"
              @click="openEditDialog"
          >
            <v-icon start>mdi-pencil</v-icon>
            Chỉnh sửa
          </v-btn>

          <v-btn
              color="error"
              variant="outlined"
              @click="openDeleteDialog"
          >
            <v-icon start>mdi-delete</v-icon>
            Xóa dự án
          </v-btn>
        </div>
      </div>

      <!-- Project Stats -->
      <v-row class="mb-6">
        <v-col cols="12" sm="6" md="3">
          <v-card elevation="1">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-primary">
                {{ taskStats.total }}
              </div>
              <div class="text-caption text-medium-emphasis">Tổng nhiệm vụ</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="1">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-grey">
                {{ taskStats.todo }}
              </div>
              <div class="text-caption text-medium-emphasis">Chờ làm</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="1">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-blue">
                {{ taskStats.inProgress }}
              </div>
              <div class="text-caption text-medium-emphasis">Đang làm</div>
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card elevation="1">
            <v-card-text class="text-center">
              <div class="text-h4 font-weight-bold text-green">
                {{ taskStats.done }}
              </div>
              <div class="text-caption text-medium-emphasis">Hoàn thành</div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <!-- Tasks Section -->
      <TaskList
          :tasks="tasksStore.tasks"
          :loading="tasksStore.loading"
          @create="openCreateTaskDialog"
          @edit="openEditTaskDialog"
          @delete="openDeleteTaskDialog"
          @update-status="handleTaskStatusUpdate"
          @update-order="handleTaskOrderUpdate"
          @filter="handleTaskFilter"
          @search="handleTaskSearch"
          @view-mode-change="handleViewModeChange"
      />

      <!-- Dialogs -->
      <ProjectForm
          v-model="showProjectForm"
          :project="project"
          :loading="formLoading"
          @submit="handleProjectUpdate"
          @cancel="showProjectForm = false"
      />

      <TaskForm
          v-model="showTaskForm"
          :task="selectedTask"
          :loading="taskFormLoading"
          @submit="handleTaskSubmit"
          @cancel="closeTaskFormDialog"
      />

      <!-- Delete Dialogs -->
      <v-dialog v-model="showDeleteProjectDialog" max-width="400">
        <v-card>
          <v-card-title>Xác nhận xóa dự án</v-card-title>
          <v-card-text>
            Bạn có chắc chắn muốn xóa dự án "{{ project.title }}"?
            Tất cả nhiệm vụ trong dự án cũng sẽ bị xóa. Hành động này không thể hoàn tác.
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="showDeleteProjectDialog = false">Hủy</v-btn>
            <v-btn
                color="error"
                :loading="deleteLoading"
                @click="handleProjectDelete"
            >
              Xóa dự án
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>

      <v-dialog v-model="showDeleteTaskDialog" max-width="400">
        <v-card>
          <v-card-title>Xác nhận xóa nhiệm vụ</v-card-title>
          <v-card-text>
            Bạn có chắc chắn muốn xóa nhiệm vụ "{{ selectedTask?.title }}"?
            Hành động này không thể hoàn tác.
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="showDeleteTaskDialog = false">Hủy</v-btn>
            <v-btn
                color="error"
                :loading="deleteTaskLoading"
                @click="handleTaskDelete"
            >
              Xóa nhiệm vụ
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>

    <!-- Toast Notification -->
    <ToastNotification
        v-model="showToast"
        :message="toastMessage"
        :type="toastType"
    />
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectsStore } from '@/stores/projects'
import { useTasksStore } from '@/stores/tasks'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import TaskList from '@/components/tasks/TaskList.vue'
import ProjectForm from '@/components/projects/ProjectForm.vue'
import TaskForm from '@/components/tasks/TaskForm.vue'
import ToastNotification from '@/components/common/ToastNotification.vue'

const route = useRoute()
const router = useRouter()
const projectsStore = useProjectsStore()
const tasksStore = useTasksStore()

const projectId = parseInt(route.params.id)
const loading = ref(true)
const showProjectForm = ref(false)
const showTaskForm = ref(false)
const showDeleteProjectDialog = ref(false)
const showDeleteTaskDialog = ref(false)
const selectedTask = ref(null)
const formLoading = ref(false)
const taskFormLoading = ref(false)
const deleteLoading = ref(false)
const deleteTaskLoading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('info')

const project = computed(() => projectsStore.currentProject)

const taskStats = computed(() => {
  const tasks = tasksStore.tasks
  return {
    total: tasks.length,
    todo: tasks.filter(t => t.status === 'todo').length,
    inProgress: tasks.filter(t => t.status === 'in_progress').length,
    done: tasks.filter(t => t.status === 'done').length
  }
})

// Dialog handlers
const openEditDialog = () => {
  showProjectForm.value = true
}

const openDeleteDialog = () => {
  showDeleteProjectDialog.value = true
}

const openCreateTaskDialog = () => {
  selectedTask.value = null
  showTaskForm.value = true
}

const openEditTaskDialog = (task) => {
  selectedTask.value = task
  showTaskForm.value = true
}

const openDeleteTaskDialog = (task) => {
  selectedTask.value = task
  showDeleteTaskDialog.value = true
}

const closeTaskFormDialog = () => {
  showTaskForm.value = false
  selectedTask.value = null
}

// Action handlers
const handleProjectUpdate = async (projectData) => {
  formLoading.value = true

  try {
    await projectsStore.updateProject(projectId, projectData)
    showToastMessage('Cập nhật dự án thành công', 'success')
    showProjectForm.value = false
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    formLoading.value = false
  }
}

const handleProjectDelete = async () => {
  deleteLoading.value = true

  try {
    await projectsStore.deleteProject(projectId)
    showToastMessage('Xóa dự án thành công', 'success')
    router.push('/projects')
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    deleteLoading.value = false
  }
}

const handleTaskSubmit = async (taskData) => {
  taskFormLoading.value = true

  try {
    if (selectedTask.value) {
      await tasksStore.updateTask(selectedTask.value.id, taskData)
      showToastMessage('Cập nhật nhiệm vụ thành công', 'success')
    } else {
      await tasksStore.createTask(projectId, taskData)
      showToastMessage('Tạo nhiệm vụ thành công', 'success')
    }

    closeTaskFormDialog()
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    taskFormLoading.value = false
  }
}

const handleTaskDelete = async () => {
  deleteTaskLoading.value = true

  try {
    await tasksStore.deleteTask(selectedTask.value.id)
    showToastMessage('Xóa nhiệm vụ thành công', 'success')
    showDeleteTaskDialog.value = false
    selectedTask.value = null
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    deleteTaskLoading.value = false
  }
}

const handleTaskStatusUpdate = async (task) => {
  try {
    await tasksStore.updateTask(task.id, { status: task.status })
    showToastMessage('Cập nhật trạng thái thành công', 'success')
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  }
}

const handleTaskOrderUpdate = async (tasks) => {
  try {
    await tasksStore.updateTaskOrder(tasks)
    showToastMessage('Cập nhật thứ tự thành công', 'success')
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  }
}

const handleTaskFilter = (filters) => {
  tasksStore.setFilters(filters)
  tasksStore.fetchTasks(projectId)
}

const handleTaskSearch = (query) => {
  tasksStore.setFilters({ search: query })
  tasksStore.fetchTasks(projectId)
}

const handleViewModeChange = (mode) => {
  // Handle view mode change if needed
}

const showToastMessage = (message, type = 'info') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
}

onMounted(async () => {
  try {
    await projectsStore.fetchProject(projectId)
    await tasksStore.fetchTasks(projectId)
    tasksStore.setupRealTimeListeners(projectId)
  } catch (error) {
    console.log('bbbbbb', error)
    showToastMessage('Lỗi khi tải thông tin dự án', 'error')
  } finally {
    loading.value = false
  }
})

onUnmounted(() => {
  tasksStore.cleanupRealTimeListeners(projectId)
  projectsStore.clearCurrentProject()
  tasksStore.clearCurrentTask()
})
</script>