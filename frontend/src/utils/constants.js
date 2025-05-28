export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export const TASK_STATUS = {
    TODO: 'todo',
    IN_PROGRESS: 'in_progress',
    DONE: 'done'
}

export const TASK_PRIORITY = {
    LOW: 'low',
    MEDIUM: 'medium',
    HIGH: 'high'
}

export const PROJECT_STATUS = {
    ACTIVE: 'active',
    INACTIVE: 'inactive'
}

export const TASK_STATUS_LABELS = {
    [TASK_STATUS.TODO]: 'Chờ làm',
    [TASK_STATUS.IN_PROGRESS]: 'Đang làm',
    [TASK_STATUS.DONE]: 'Hoàn thành'
}

export const TASK_PRIORITY_LABELS = {
    [TASK_PRIORITY.LOW]: 'Thấp',
    [TASK_PRIORITY.MEDIUM]: 'Trung bình',
    [TASK_PRIORITY.HIGH]: 'Cao'
}

export const PROJECT_STATUS_LABELS = {
    [PROJECT_STATUS.ACTIVE]: 'Hoạt động',
    [PROJECT_STATUS.INACTIVE]: 'Không hoạt động'
}

export const TASK_STATUS_COLORS = {
    [TASK_STATUS.TODO]: 'grey',
    [TASK_STATUS.IN_PROGRESS]: 'blue',
    [TASK_STATUS.DONE]: 'green'
}

export const TASK_PRIORITY_COLORS = {
    [TASK_PRIORITY.LOW]: 'green',
    [TASK_PRIORITY.MEDIUM]: 'orange',
    [TASK_PRIORITY.HIGH]: 'red'
}