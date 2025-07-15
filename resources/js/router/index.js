import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import DashboardIndex from '../views/admin/DashboardIndex.vue'
import HomeGrid from '../views/admin/HomeGrid.vue'
import ProjectsList from '../views/admin/Projects.vue'
import CreateProject from '../views/admin/CreateProject.vue'
import Workflow from '../views/admin/Workflow.vue'
import Services from '../views/admin/Services.vue'
import ForgotPassword from '../views/ForgotPassword.vue'
import ResetPassword from '../views/ResetPassword.vue'
import axios from 'axios'

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
  path: '/forgot-password',
  component: ForgotPassword
  },
  {
  path: '/reset-password',
  component: ResetPassword
  },
  {
    path: '/',
    component: Dashboard,
    meta: { requiresAuth: true },
    children: [
      { path: 'dashboard', component: DashboardIndex },
      { path: 'home-grid', component: HomeGrid },
      { path: 'project', component: ProjectsList },
      { path: 'project/list', component: ProjectsList },
      { path: 'project/create', component: CreateProject },
      {
        path: 'project/edit/:id',
        name: 'EditProject',
        component: CreateProject
      },
      { path: 'workflow', component: Workflow },
      { path: 'services', component: Services }
    ]
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
  routes
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
