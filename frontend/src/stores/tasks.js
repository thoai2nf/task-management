import { defineStore } from 'pinia'
import { taskService } from '@/services/taskService'
import echo from '@/plugins/pusher'

export const useTasksStore = defineStore('tasks', {
    state: () => ({
        tasks: [],
        currentTask: null,
        loading: false,
        error: null,
        filters: {
            status: '',
            priority: '',
            search: '',
            sort_by: 'order',
            sort_direction: 'asc'
        }
    }),

    getters: {
        todoTasks: (state) => state.tasks.filter(t => t.status === 'todo'),
        inProgressTasks: (state) => state.tasks.filter(t => t.status === 'in_progress'),
        doneTasks: (state) => state.tasks.filter(t => t.status === 'done'),

        tasksByStatus: (state) => (status) => state.tasks.filter(t => t.status === status),
        tasksByPriority: (state) => (priority) => state.tasks.filter(t => t.priority === priority),
    },

    actions: {
        async fetchTasks(projectId, params = {}) {
            this.loading = true
            this.error = null

            try {
                const response = await taskService.getByProject(projectId, {
                    ...this.filters,
                    ...params
                })

                if (response.success) {
                    this.tasks = response.data
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi tải danh sách nhiệm vụ'
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchTask(id) {
            this.loading = true
            this.error = null

            try {
                const response = await taskService.getById(id)

                if (response.success) {
                    this.currentTask = response.data
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi tải thông tin nhiệm vụ'
                throw error
            } finally {
                this.loading = false
            }
        },

        async createTask(projectId, taskData) {
            this.loading = true
            this.error = null

            try {
                const response = await taskService.create(projectId, taskData)

                if (response.success) {
                    this.tasks.push(response.data)
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi tạo nhiệm vụ'
                throw error
            } finally {
                this.loading = false
            }
        },

        async updateTask(id, taskData) {
            this.loading = true
            this.error = null

            try {
                const response = await taskService.update(id, taskData)

                if (response.success) {
                    const index = this.tasks.findIndex(t => t.id === id)
                    if (index !== -1) {
                        this.tasks[index] = response.data
                    }

                    if (this.currentTask?.id === id) {
                        this.currentTask = response.data
                    }
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi cập nhật nhiệm vụ'
                throw error
            } finally {
                this.loading = false
            }
        },

        async deleteTask(id) {
            this.loading = true
            this.error = null

            try {
                const response = await taskService.delete(id)

                if (response.success) {
                    this.tasks = this.tasks.filter(t => t.id !== id)

                    if (this.currentTask?.id === id) {
                        this.currentTask = null
                    }
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi xóa nhiệm vụ'
                throw error
            } finally {
                this.loading = false
            }
        },

        async updateTaskOrder(tasks) {
            try {
                const response = await taskService.updateOrder(tasks)

                if (response.success) {
                    tasks.forEach(taskData => {
                        const task = this.tasks.find(t => t.id === taskData.id)
                        if (task) {
                            task.order = taskData.order
                        }
                    })

                    this.tasks.sort((a, b) => a.order - b.order)
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi cập nhật thứ tự nhiệm vụ'
                throw error
            }
        },

        setFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters }
        },

        clearCurrentTask() {
            this.currentTask = null
        },

        // Real-time event handlers
        handleTaskCreated(task) {
            if (!this.tasks.find(t => t.id === task.id)) {
                this.tasks.push(task)
            }
        },

        handleTaskUpdated(task) {
            const index = this.tasks.findIndex(t => t.id === task.id)
            if (index !== -1) {
                this.tasks[index] = task
            }

            if (this.currentTask?.id === task.id) {
                this.currentTask = task
            }
        },

        handleTaskDeleted(task) {
            this.tasks = this.tasks.filter(t => t.id !== task.id)

            if (this.currentTask?.id === task.id) {
                this.currentTask = null
            }
        },

        setupRealTimeListeners(projectId) {
            echo.private(`project.${projectId}`)
                .listen('.task.created', (e) => {
                    this.handleTaskCreated(e.task)
                })
                .listen('.task.updated', (e) => {
                    this.handleTaskUpdated(e.task)
                })
                .listen('.task.deleted', (e) => {
                    this.handleTaskDeleted(e.task)
                })
        },

        cleanupRealTimeListeners(projectId) {
            echo.leave(`project.${projectId}`)
        }
    }
})