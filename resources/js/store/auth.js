import { reactive, computed } from 'vue'

const state = reactive({
  user: JSON.parse(localStorage.getItem('user')) || null,
  token: localStorage.getItem('token') || null
})

export const useAuth = () => {
 

const setAuth = (userData, tokenData) => {
    // Force clear old storage
    localStorage.clear()
    
    // Update reactive state
    state.user = userData
    state.token = tokenData
    
    // Write fresh user data
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('token', tokenData)
  }

  const logout = () => {
    // 1. Reset reactive state to null
    state.user = null
    state.token = null

    // 2. Wipe browser storage
    localStorage.clear()
  }
  const isAuthenticated = computed(() => !!state.token)
  const isAdmin = computed(() => state.user && (state.user.role === 'admin' || state.user.role === 'super_admin'))
  const isSuperAdmin = computed(() => state.user && state.user.role === 'super_admin')

  return {
    state,
    setAuth,
    logout,
    isAuthenticated,
    isAdmin,
    isUser: computed(() => state.user && state.user.role === 'user'),
    isSuperAdmin
  }
}