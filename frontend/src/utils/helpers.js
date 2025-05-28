import { format, parseISO } from 'date-fns'
import { vi } from 'date-fns/locale'

export const formatDate = (date, formatStr = 'dd/MM/yyyy') => {
    if (!date) return ''

    try {
        const parsedDate = typeof date === 'string' ? parseISO(date) : date
        return format(parsedDate, formatStr, { locale: vi })
    } catch (error) {
        console.error('Error formatting date:', error)
        return ''
    }
}

export const formatDateTime = (date) => {
    return formatDate(date, 'dd/MM/yyyy HH:mm')
}

export const truncateText = (text, maxLength = 100) => {
    if (!text) return ''
    if (text.length <= maxLength) return text
    return text.substring(0, maxLength) + '...'
}

export const debounce = (func, wait) => {
    let timeout
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout)
            func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
    }
}

export const generateId = () => {
    return Math.random().toString(36).substr(2, 9)
}

export const capitalize = (str) => {
    if (!str) return ''
    return str.charAt(0).toUpperCase() + str.slice(1)
}