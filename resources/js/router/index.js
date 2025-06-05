import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import axios from 'axios'

const routes = [
  { path: '/login', component: Login },
  { path: '/', component: Dashboard, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory('/admin/'),
  routes,
})

// Глобальный guard
router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)

  if (!requiresAuth) {
    return next()
  }

  try {
    await axios.get('/api/admin/me')
    next()
  } catch (err) {
    console.warn('Не авторизован, перенаправляю на /login')
    next('/login')
  }
})

export default router
