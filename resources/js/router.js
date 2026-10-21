import { createRouter, createWebHistory } from 'vue-router'
import DebaterRanking from './components/DebaterRanking.vue' 
import DebaterProfile from './components/DebaterProfile.vue' 

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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router