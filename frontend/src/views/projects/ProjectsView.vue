<template>
  <v-container fluid>
    <div class="d-flex justify-space-between align-center mb-6">
      <div>
        <h1 class="text-h4 font-weight-bold">Dự án</h1>
        <p class="text-subtitle-1 text-medium-emphasis">
          Quản lý tất cả dự án của bạn
        </p>
      </div>
    </div>

    <ProjectList
        :projects="projectsStore.projects"
        :loading="projectsStore.loading"
        :pagination="projectsStore.pagination"
        @create="openCreateDialog"
        @edit="openEditDialog"
        @delete="openDeleteDialog"
        @filter="handleFilter"
        @search="handleSearch"
        @sort="handleSort"
        @page-change="handlePageChange"
    />

    <!-- Project Form Dialog -->
    <ProjectForm
        v-model="showProjectForm"
        :project="selectedProject"
        :loading="formLoading"
        @submit="handleSubmit"
        @cancel="closeFormDialog"
    />

    <!-- Delete Confirmation Dialog -->
    <v-dialog v-model="showDeleteDialog" max-width="400">
      <v-card>
        <v-card-title>Xác nhận xóa</v-card-title>
        <v-card-text>
          Bạn có chắc chắn muốn xóa dự án "{{ selectedProject?.title }}"?
          Hành động này không thể hoàn tác.
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="showDeleteDialog = false">Hủy</v-btn>
          <v-btn
              color="error"
              :loading="deleteLoading"
              @click="handleDelete"
          >
            Xóa
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <ToastNotification
        v-model="showToast"
        :message="toastMessage"
        :type="toastType"
    />
  </v-container>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import {useProjectsStore} from '@/stores/projects'
import ProjectList from '@/components/projects/ProjectList.vue'
import ProjectForm from '@/components/projects/ProjectForm.vue'
import ToastNotification from '@/components/common/ToastNotification.vue'

const projectsStore = useProjectsStore()

const showProjectForm = ref(false)
const showDeleteDialog = ref(false)
const selectedProject = ref(null)
const formLoading = ref(false)
const deleteLoading = ref(false)
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('info')

const openCreateDialog = () => {
  selectedProject.value = null
  showProjectForm.value = true;
  console.log('openCreateDialog', showProjectForm)
}

const openEditDialog = (project) => {
  selectedProject.value = project
  showProjectForm.value = true
}

const openDeleteDialog = (project) => {
  selectedProject.value = project
  showDeleteDialog.value = true
}

const closeFormDialog = () => {
  showProjectForm.value = false
  selectedProject.value = null
}

const handleSubmit = async (projectData) => {
  formLoading.value = true

  try {
    if (selectedProject.value) {
      await projectsStore.updateProject(selectedProject.value.id, projectData)
      showToastMessage('Cập nhật dự án thành công', 'success')
    } else {
      await projectsStore.createProject(projectData)
      showToastMessage('Tạo dự án thành công', 'success')
    }

    closeFormDialog()
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    formLoading.value = false
  }
}

const handleDelete = async () => {
  deleteLoading.value = true

  try {
    await projectsStore.deleteProject(selectedProject.value.id)
    showToastMessage('Xóa dự án thành công', 'success')
    showDeleteDialog.value = false
    selectedProject.value = null
  } catch (error) {
    showToastMessage(error.message || 'Có lỗi xảy ra', 'error')
  } finally {
    deleteLoading.value = false
  }
}

const handleFilter = (filters) => {
  projectsStore.setFilters(filters)
  projectsStore.fetchProjects()
}

const handleSearch = (query) => {
  projectsStore.setFilters({search: query})
  projectsStore.fetchProjects()
}

const handleSort = (sortOptions) => {
  projectsStore.setFilters(sortOptions)
  projectsStore.fetchProjects()
}

const handlePageChange = (page) => {
  projectsStore.setPage(page)
  projectsStore.fetchProjects()
}

const showToastMessage = (message, type = 'info') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
}

onMounted(() => {
  projectsStore.fetchProjects()
})
</script>