<template>
  <div class="admin-layout">
    <!-- Верхнее меню -->
    <header class="admin-header">
      <h1>Tero Design Admin</h1>
      <button class="logout-btn" @click="logout">Выйти</button>
    </header>

    <!-- Боковое меню -->
    <aside class="admin-sidebar">
      <nav>
        <ul>
          <li><span class="nav-section">Навигация</span></li>

          <!-- Панель управления -->
          <li>
            <RouterLink to="/dashboard" class="active-tab">📊 Панель управления</RouterLink>
          </li>

          <!-- ✅ Новая кнопка Home Grid -->
          <li>
            <RouterLink to="/home-grid">🏠 Home Grid</RouterLink>
          </li>

          <li><RouterLink to="/projects">📂 Проекты</RouterLink></li>
          <li><RouterLink to="/workflow">🔁 Workflow</RouterLink></li>
          <li><RouterLink to="/services">🛠️ Услуги</RouterLink></li>
        </ul>
      </nav>
    </aside>

    <!-- Контент -->
    <main class="admin-content">
      <div class="dashboard-grid">
        <div class="dashboard-col">
          <DashboardIndex />
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import axios from 'axios'
import DashboardIndex from '../views/admin/DashboardIndex.vue'
import HomeGrid from '../views/admin/HomeGrid.vue'

const router = useRouter()

const logout = async () => {
  await axios.post('/api/admin/logout')
  router.push('/login')
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  height: 100vh;
  overflow: hidden;
}
.admin-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: #2c3e50;
  color: #ecf0f1;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;
  z-index: 10;
}
.admin-sidebar {
  position: fixed;
  top: 60px;
  left: 0;
  width: 220px;
  height: calc(100vh - 60px);
  background: #1a252f;
  padding: 20px 0;
}
.admin-sidebar ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.admin-sidebar li {
  margin-bottom: 10px;
}
.admin-sidebar a {
  color: #bdc3c7;
  text-decoration: none;
  padding: 10px 20px;
  display: block;
}
.admin-sidebar .active-tab {
  background: #34495e;
  color: white;
}
.admin-content {
  margin-left: 220px;
  margin-top: 60px;
  padding: 20px;
  background: #f4f6f9;
  width: calc(100% - 220px);
  overflow-y: auto;
}
.logout-btn {
  background: #e74c3c;
  color: white;
  border: none;
  padding: 6px 12px;
  cursor: pointer;
}

/* ✅ Сетка */
.dashboard-grid {
  display: flex;
  gap: 20px;
}
.dashboard-col {
  flex: 1;
}
</style>
