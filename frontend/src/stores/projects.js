import { defineStore } from 'pinia'
import { projectService } from '@/services/projectService'

export const useProjectsStore = defineStore('projects', {
    state: () => ({
        projects: [],
        currentProject: null,
        loading: false,
        error: null,
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 15,
            total: 0
        },
        filters: {
            status: '',
            search: '',
            sort_by: 'created_at',
            sort_direction: 'desc'
        }
    }),

    getters: {
        activeProjects: (state) => state.projects.filter(p => p.status === 'active'),
        inactiveProjects: (state) => state.projects.filter(p => p.status === 'inactive'),
    },

    actions: {
        async fetchProjects(params = {}) {
            this.loading = true
            this.error = null

            try {
                const response = await projectService.getAll({
                    ...this.filters,
                    ...params,
                    page: this.pagination.current_page
                })

                if (response.success) {
                    this.projects = response.data.data
                    this.pagination = {
                        current_page: response.data.current_page,
                        last_page: response.data.last_page,
                        per_page: response.data.per_page,
                        total: response.data.total
                    }
                }
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi tải danh sách dự án'
                throw error
            } finally {
                this.loading = false
            }
        },

        async fetchProject(id) {
            this.loading = true
            this.error = null

            try {
                const response = await projectService.getById(id)

                if (response.success) {
                    this.currentProject = response.data
                }

                return response
            } catch (error) {
                console.log('aaaaa', error)
                this.error = error.response?.data?.message || 'Lỗi khi tải thông tin dự án'
                throw error
            } finally {
                this.loading = false
            }
        },

        async createProject(projectData) {
            this.loading = true
            this.error = null

            try {
                const response = await projectService.create(projectData)

                if (response.success) {
                    await this.fetchProjects()
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi tạo dự án'
                throw error
            } finally {
                this.loading = false
            }
        },

        async updateProject(id, projectData) {
            this.loading = true
            this.error = null

            try {
                const response = await projectService.update(id, projectData)

                if (response.success) {
                    const index = this.projects.findIndex(p => p.id === id)
                    if (index !== -1) {
                        this.projects[index] = response.data
                    }

                    if (this.currentProject?.id === id) {
                        this.currentProject = response.data
                    }
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi cập nhật dự án'
                throw error
            } finally {
                this.loading = false
            }
        },

        async deleteProject(id) {
            this.loading = true
            this.error = null

            try {
                const response = await projectService.delete(id)

                if (response.success) {
                    this.projects = this.projects.filter(p => p.id !== id)

                    if (this.currentProject?.id === id) {
                        this.currentProject = null
                    }
                }

                return response
            } catch (error) {
                this.error = error.response?.data?.message || 'Lỗi khi xóa dự án'
                throw error
            } finally {
                this.loading = false
            }
        },

        setFilters(newFilters) {
            this.filters = { ...this.filters, ...newFilters }
        },

        setPage(page) {
            this.pagination.current_page = page
        },

        clearCurrentProject() {
            this.currentProject = null
        }
    }
})