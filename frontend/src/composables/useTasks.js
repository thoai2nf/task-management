import { computed, ref } from 'vue'
import { useTasksStore } from '@/stores/tasks'

export function useTasks() {
    const tasksStore = useTasksStore()
    const loading = ref(false)
    const error = ref(null)

    const tasks = computed(() => tasksStore.tasks)
    const currentTask = computed(() => tasksStore.currentTask)
    const todoTasks = computed(() => tasksStore.todoTasks)
    const inProgressTasks = computed(() => tasksStore.inProgressTasks)
    const doneTasks = computed(() => tasksStore.doneTasks)

    const fetchTasks = async (projectId, params = {}) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.fetchTasks(projectId, params)
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const fetchTask = async (id) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.fetchTask(id)
        } catch (err) {
            error.value = err.message
        } finally {
            loading.value = false
        }
    }

    const createTask = async (projectId, taskData) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.createTask(projectId, taskData)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const updateTask = async (id, taskData) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.updateTask(id, taskData)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const deleteTask = async (id) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.deleteTask(id)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const updateTaskOrder = async (tasks) => {
        loading.value = true
        error.value = null

        try {
            await tasksStore.updateTaskOrder(tasks)
            return { success: true }
        } catch (err) {
            error.value = err.message
            return { success: false, error: err.message }
        } finally {
            loading.value = false
        }
    }

    const setFilters = (filters) => {
        tasksStore.setFilters(filters)
    }

    const setupRealTimeListeners = (projectId) => {
        tasksStore.setupRealTimeListeners(projectId)
    }

    const cleanupRealTimeListeners = (projectId) => {
        tasksStore.cleanupRealTimeListeners(projectId)
    }

    return {
        tasks,
        currentTask,
        todoTasks,
        inProgressTasks,
        doneTasks,
        loading: computed(() => loading.value || tasksStore.loading),
        error: computed(() => error.value || tasksStore.error),
        fetchTasks,
        fetchTask,
        createTask,
        updateTask,
        deleteTask,
        updateTaskOrder,
        setFilters,
        setupRealTimeListeners,
        cleanupRealTimeListeners
    }
}