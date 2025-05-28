<template>
  <v-container fluid>
    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold">Dashboard</h1>
        <p class="text-subtitle-1 text-medium-emphasis">
          Chào mừng {{ authStore.user?.name }}!
        </p>
      </div>

      <v-btn
          color="primary"
          size="large"
          to="/projects"
      >
        <v-icon start>mdi-folder-multiple</v-icon>
        Xem tất cả dự án
      </v-btn>
    </div>

    <!-- Statistics Cards -->
    <v-row class="mb-6">
      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center">
              <v-avatar color="primary" class="me-3">
                <v-icon color="white">mdi-folder-multiple</v-icon>
              </v-avatar>
              <div>
                <div class="text-h5 font-weight-bold">{{ stats.totalProjects }}</div>
                <div class="text-caption text-medium-emphasis">Tổng dự án</div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center">
              <v-avatar color="orange" class="me-3">
                <v-icon color="white">mdi-format-list-checks</v-icon>
              </v-avatar>
              <div>
                <div class="text-h5 font-weight-bold">{{ stats.totalTasks }}</div>
                <div class="text-caption text-medium-emphasis">Tổng nhiệm vụ</div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center">
              <v-avatar color="blue" class="me-3">
                <v-icon color="white">mdi-clock-outline</v-icon>
              </v-avatar>
              <div>
                <div class="text-h5 font-weight-bold">{{ stats.inProgressTasks }}</div>
                <div class="text-caption text-medium-emphasis">Đang thực hiện</div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" md="3">
        <v-card elevation="2">
          <v-card-text>
            <div class="d-flex align-center">
              <v-avatar color="green" class="me-3">
                <v-icon color="white">mdi-check-circle</v-icon>
              </v-avatar>
              <div>
                <div class="text-h5 font-weight-bold">{{ stats.completedTasks }}</div>
                <div class="text-caption text-medium-emphasis">Hoàn thành</div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Recent Projects and Tasks -->
    <v-row>
      <v-col cols="12" md="6">
        <v-card elevation="2">
          <v-card-title class="d-flex justify-space-between align-center">
            <span>Dự án gần đây</span>
            <v-btn
                variant="text"
                color="primary"
                size="small"
                to="/projects"
            >
              Xem tất cả
            </v-btn>
          </v-card-title>

          <v-card-text>
            <div v-if="loading" class="text-center py-4">
              <LoadingSpinner size="30" />
            </div>

            <div v-else-if="recentProjects.length === 0" class="text-center py-4">
              <v-icon size="48" color="grey-lighten-2">mdi-folder-outline</v-icon>
              <p class="text-body-2 text-medium-emphasis mt-2">Chưa có dự án nào</p>
            </div>

            <v-list v-else lines="two">
              <v-list-item
                  v-for="project in recentProjects"
                  :key="project.id"
                  :to="`/projects/${project.id}`"
              >
                <template v-slot:prepend>
                  <v-avatar :color="project.status === 'active' ? 'success' : 'grey'">
                    <v-icon color="white">mdi-folder</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title>{{ project.title }}</v-list-item-title>
                <v-list-item-subtitle>
                  {{ project.tasks?.length || 0 }} nhiệm vụ •
                  {{ formatDate(project.created_at) }}
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card elevation="2">
          <v-card-title>
            Nhiệm vụ cần chú ý
          </v-card-title>

          <v-card-text>
            <div v-if="loading" class="text-center py-4">
              <LoadingSpinner size="30" />
            </div>

            <div v-else-if="urgentTasks.length === 0" class="text-center py-4">
              <v-icon size="48" color="grey-lighten-2">mdi-check-circle-outline</v-icon>
              <p class="text-body-2 text-medium-emphasis mt-2">Không có nhiệm vụ cần chú ý</p>
            </div>

            <v-list v-else lines="three">
              <v-list-item
                  v-for="task in urgentTasks"
                  :key="task.id"
              >
                <template v-slot:prepend>
                  <v-avatar :color="getPriorityColor(task.priority)">
                    <v-icon color="white">mdi-flag</v-icon>
                  </v-avatar>
                </template>

                <v-list-item-title>{{ task.title }}</v-list-item-title>
                <v-list-item-subtitle>
                  {{ task.project?.title }} •
                  <span v-if="task.due_date">
                    Hết hạn: {{ formatDate(task.due_date) }}
                  </span>
                </v-list-item-subtitle>

                <template v-slot:append>
                  <v-chip
                      :color="getStatusColor(task.status)"
                      size="small"
                  >
                    {{ getStatusText(task.status) }}
                  </v-chip>
                </template>
              </v-list-item>
            </v-list>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useProjectsStore } from '@/stores/projects'
import { formatDate } from '@/utils/helpers'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const authStore = useAuthStore()
const projectsStore = useProjectsStore()

const loading = ref(true)
const recentProjects = ref([])
const urgentTasks = ref([])

const stats = computed(() => {
  const projects = recentProjects.value
  const allTasks = projects.reduce((acc, project) => {
    return [...acc, ...(project.tasks || [])]
  }, [])

  return {
    totalProjects: projects.length,
    totalTasks: allTasks.length,
    inProgressTasks: allTasks.filter(task => task.status === 'in_progress').length,
    completedTasks: allTasks.filter(task => task.status === 'done').length
  }
})

const getPriorityColor = (priority) => {
  const colors = {
    low: 'green',
    medium: 'orange',
    high: 'red'
  }
  return colors[priority] || 'grey'
}

const getStatusColor = (status) => {
  const colors = {
    todo: 'grey',
    in_progress: 'blue',
    done: 'green'
  }
  return colors[status] || 'grey'
}

const getStatusText = (status) => {
  const texts = {
    todo: 'Chờ làm',
    in_progress: 'Đang làm',
    done: 'Hoàn thành'
  }
  return texts[status] || 'Không xác định'
}

onMounted(async () => {
  try {
    await projectsStore.fetchProjects({ per_page: 5 })
    recentProjects.value = projectsStore.projects.slice(0, 5)

    const allTasks = recentProjects.value.reduce((acc, project) => {
      const tasks = (project.tasks || []).map(task => ({
        ...task,
        project: { id: project.id, title: project.title }
      }))
      return [...acc, ...tasks]
    }, [])

    urgentTasks.value = allTasks
        .filter(task =>
            task.priority === 'high' ||
            task.status === 'in_progress' ||
            (task.due_date && new Date(task.due_date) <= new Date(Date.now() + 7 * 24 * 60 * 60 * 1000))
        )
        .slice(0, 5)
  } catch (error) {
    console.error('Error loading dashboard data:', error)
  } finally {
    loading.value = false
  }
})
</script>