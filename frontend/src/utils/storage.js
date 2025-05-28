export const storage = {
    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key)
            return item ? JSON.parse(item) : defaultValue
        } catch (error) {
            console.error('Error getting item from storage:', error)
            return defaultValue
        }
    },

    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value))
        } catch (error) {
            console.error('Error setting item to storage:', error)
        }
    },

    remove(key) {
        try {
            localStorage.removeItem(key)
        } catch (error) {
            console.error('Error removing item from storage:', error)
        }
    },

    clear() {
        try {
            localStorage.clear()
        } catch (error) {
            console.error('Error clearing storage:', error)
        }
    }
}