import { computed, ref } from 'vue'
import { useProjectsStore } from '@/stores/projects'

export function useProjects() {
    const projectsStore = useProjectsStore()
    const loading = ref(false)
    const error = ref(null)

    const projects = computed(() => projectsStore.projects)
    const currentProject = computed(() => projectsStore.currentProject)
    const pagination = computed(() => projectsStore.pagination)

    const fetchProjects = async (params = {}) => {
        loading.value = true
        error.value = null

        try {
            await projectsStore.fetchProjects(params)
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchProject = async (id) => {
        loading.value = true
        error.value = null

        try {
            await projectsStore.fetchProject(id)
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const createProject = async (projectData) => {
        loading.value = true
        error.value = null

        try {
            await projectsStore.createProject(projectData)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const updateProject = async (id, projectData) => {
        loading.value = true
        error.value = null

        try {
            await projectsStore.updateProject(id, projectData)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const deleteProject = async (id) => {
        loading.value = true
        error.value = null

        try {
            await projectsStore.deleteProject(id)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const setFilters = (filters) => {
        projectsStore.setFilters(filters)
    }

    const setPage = (page) => {
        projectsStore.setPage(page)
    }

    return {
        projects,
        currentProject,
        pagination,
        loading: computed(() => loading.value || projectsStore.loading),
        error: computed(() => error.value || projectsStore.error),
        fetchProjects,
        fetchProject,
        createProject,
        updateProject,
        deleteProject,
        setFilters,
        setPage
    }
}