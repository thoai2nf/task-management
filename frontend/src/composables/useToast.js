import { ref } from 'vue'

export function useToast() {
    const show = ref(false)
    const message = ref('')
    const type = ref('info')
    const timeout = ref(5000)

    const showToast = (msg, toastType = 'info', duration = 5000) => {
        message.value = msg
        type.value = toastType
        timeout.value = duration
        show.value = true
    }

    const showSuccess = (msg, duration = 5000) => {
        showToast(msg, 'success', duration)
    }

    const showError = (msg, duration = 7000) => {
        showToast(msg, 'error', duration)
    }

    const showWarning = (msg, duration = 6000) => {
        showToast(msg, 'warning', duration)
    }

    const showInfo = (msg, duration = 5000) => {
        showToast(msg, 'info', duration)
    }

    const hide = () => {
        show.value = false
    }

    return {
        show,
        message,
        type,
        timeout,
        showToast,
        showSuccess,
        showError,
        showWarning,
        showInfo,
        hide
    }
}