import { reactive, computed } from 'vue'

const state = reactive({
  user: JSON.parse(localStorage.getItem('user')) || null,
  token: localStorage.getItem('token') || null
})

export const useAuth = () => {
  const setAuth = (userData, tokenData) => {
    state.user = userData
    state.token = tokenData
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('token', tokenData)
  }

  const logout = () => {
    state.user = null
    state.token = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  const isAuthenticated = computed(() => !!state.token)
  const isAdmin = computed(() => state.user && (state.user.role === 'admin' || state.user.role === 'super_admin'))

  return {
    state,
    setAuth,
    logout,
    isAuthenticated,
    isAdmin
  }
}