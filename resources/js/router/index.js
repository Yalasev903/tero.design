import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import ShowreelPreview from '@/components/admin/ShowreelPreview.vue'
import HomeGrid from '../views/admin/HomeGrid.vue'
import Projects from '../views/admin/Projects.vue'
import Workflow from '../views/admin/Workflow.vue'
import Services from '../views/admin/Services.vue'
import axios from 'axios'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
  path: '/home-grid',
  component: HomeGrid,
  meta: { requiresAuth: true }
  },
  {
    path: '/projects',
    component: Projects,
    meta: { requiresAuth: true }
  },
  {
    path: '/workflow',
    component: Workflow,
    meta: { requiresAuth: true }
  },
  {
    path: '/services',
    component: Services,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/dashboard'
  }
]

const router = createRouter({
  history: createWebHistory('/admin/'),
  routes,
})

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  if (!requiresAuth) return next()
  try {
    await axios.get('/api/admin/me')
    next()
  } catch {
    next('/login')
  }
})

export default router
