import { createRouter, createWebHistory } from 'vue-router'
import DebaterRanking from './components/DebaterRanking.vue' 
import DebaterProfile from './components/DebaterProfile.vue' 



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
    name: 'home',
    component: DebaterRanking
  },
  {
    path: '/debaters/:id',
     name: 'debater.show',
    component: DebaterProfile,
    props: true
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

// Navigation Guard: Protect /admin routes
router.beforeEach((to, from, next) => {
  const auth = useAuth()
  
  // Read token & user directly to guarantee no timing sync delay
  const token = localStorage.getItem('token') || auth.state.token
  const user = JSON.parse(localStorage.getItem('user') || 'null') || auth.state.user

  const isUserAuthenticated = !!token
  const isUserAdmin = user && (user.role === 'admin' || user.role === 'super_admin')

  if (to.meta.requiresAdmin) {
    if (!isUserAuthenticated || !isUserAdmin) {
      console.log('Access denied. Redirecting to admin login.')
      return next({ name: 'AdminLogin' })
    }
  }

  if (to.name === 'AdminLogin' && isUserAuthenticated && isUserAdmin) {
    console.log('Already logged in as admin. Redirecting to admin dashboard.')
    return next({ name: 'AdminOverview' })
  }

  next()
})

export default router