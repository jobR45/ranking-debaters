import { createRouter, createWebHistory } from 'vue-router'
import PublicLayout from './components/PublicLayout.vue'
import Welcome from './components/Welcome.vue'
import DebaterRanking from './components/DebaterRanking.vue' 
import DebaterProfile from './components/DebaterProfile.vue' 
import Debates from './components/Debates.vue'
import HowWeRank from './components/HowWeRank.vue'

// Admin Views
import AdminLogin from './components/admin/AdminLogin.vue'
import AdminLayout from './components/admin/AdminLayout.vue' 
import AdminOverview from './components/admin/AdminOverview.vue' 
import AdminDebaters from './components/admin/AdminDebaters.vue'
import AdminDebaterDetails from './components/admin/AdminDebaterDetails.vue'
import AdminAudienceReviews from './components/admin/AdminAudienceReviews.vue' 
import AdminUsers from './components/admin/AdminUsers.vue' 
import { useAuth } from './store/auth.js' 

const routes = [
  {
    path: '/',
    component: PublicLayout,
    children: [
      {
        path: '',
        name: 'Welcome',
        component: Welcome
      },
      {
        path: 'ranking',
        name: 'DebaterRanking',
        component: DebaterRanking
      },
      {
        path: 'debates',
        name: 'Debates',
        component: Debates
      },
      {
        path: 'how-we-rank',
        name: 'HowWeRank',
        component: HowWeRank
      },
      {
        path: 'debaters/:id',
        name: 'DebaterProfile',
        component: DebaterProfile,
        props: true
      }
    ]
  },

  // Admin Login Route
  { path: '/admin/login', name: 'AdminLogin', component: AdminLogin },

  // Admin Dashboard Routes (Protected)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAdmin: true },
    children: [
    { path: '', name: 'AdminOverview', component: AdminOverview },
      { path: 'debaters', name: 'AdminDebaters', component: AdminDebaters },
      { path: 'debaters/:id', name: 'AdminDebaterDetails', component: AdminDebaterDetails, props: true },
      { path: 'reviews', redirect: { name: 'AdminDebaters' } },
      { path: 'audience-reviews', name: 'AdminAudienceReviews', component: AdminAudienceReviews },
      { path: 'users', name: 'AdminUsers', component: AdminUsers },
    ]
  },

  // Fallback Catch-all
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from, next) => {
  const auth = useAuth()


  // 1. Always read the LATEST values from localStorage
  const token = localStorage.getItem('token')
  const user = JSON.parse(localStorage.getItem('user') || 'null')

  const isUserAuthenticated = !!token
  const isUserAdmin = user && (user.role === 'admin' || user.role === 'super_admin')

  // 2. Validate route permissions
  if (to.meta.requiresAdmin) {
    if (!isUserAuthenticated || !isUserAdmin) {
      console.warn('Access denied: Admin route requires authentication and admin role.')
      auth.logout()
      return next({ name: 'AdminLogin' })
    }

    // Optional: Validate token with backend
    try {
      const res = await fetch('/api/user/me', {
        headers: { Authorization: `Bearer ${token}` }
      })
      if (!res.ok) {
        console.warn('Token validation failed. Logging out.')
        auth.logout()
        return next({ name: 'AdminLogin' })
      }
    } catch (err) {
      console.error('Error validating token:', err)
      auth.logout()
      return next({ name: 'AdminLogin' })
    }
  }

  if (to.name === 'AdminLogin' && isUserAuthenticated && isUserAdmin) {
    return next({ name: 'AdminOverview' })
  }

  next()
})

export default router