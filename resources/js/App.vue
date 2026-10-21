<template>
  <router-view />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuth } from './store/auth.js'

const auth = useAuth()

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search)
  const token = urlParams.get('token')

  if (token) {
    localStorage.setItem('token', token)
    window.history.replaceState({}, document.title, window.location.pathname)
    
    // Fetch user details with the new token
    try {
      const res = await fetch('/api/user/me', {
        headers: { 'Authorization': `Bearer ${token}` }
      })
      if (res.ok) {
        const user = await res.json()
        auth.setAuth(user, token)
      }
    } catch (err) {
      console.error('Failed to fetch authenticated user:', err)
    }
  }
})
</script>