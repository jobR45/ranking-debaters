import { reactive, computed } from 'vue'

const state = reactive({
  user: JSON.parse(localStorage.getItem('user')) || null,
  token: localStorage.getItem('token') || null,
})

export const useAuth = () => {
  const setAuth = (userData, userToken) => {
    state.user = userData
    state.token = userToken
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('token', userToken)
  }

  const logout = () => {
    state.user = null
    state.token = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  const isAuthenticated = computed(() => !!state.token)
  const isAdmin = computed(() => state.user?.role === 'admin')

  return {
    state,
    setAuth,
    logout,
    isAuthenticated,
    isAdmin,
  }
}