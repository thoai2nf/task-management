import api from './api'

export const taskService = {
    async getByProject(projectId, params = {}) {
        const response = await api.get(`/projects/${projectId}/tasks`, { params })
        return response.data
    },

    async getById(id) {
        const response = await api.get(`/tasks/${id}`)
        return response.data
    },

    async create(projectId, taskData) {
        const response = await api.post(`/projects/${projectId}/tasks`, taskData)
        return response.data
    },

    async update(id, taskData) {
        const response = await api.put(`/tasks/${id}`, taskData)
        return response.data
    },

    async delete(id) {
        const response = await api.delete(`/tasks/${id}`)
        return response.data
    },

    async updateOrder(tasks) {
        const response = await api.post('/tasks/update-order', { tasks })
        return response.data
    }
}