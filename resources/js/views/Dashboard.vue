<template>
  <div class="admin-layout" :class="{ 'sidebar-collapsed': !sidebarOpen }">
    <!-- Верхняя панель -->
    <header class="admin-header">
      <!-- Кнопка свернуть меню -->
      <button class="icon-btn" @click="toggleSidebar" title="Свернуть боковое меню">
        <svg viewBox="0 0 1024 1024" width="20" height="20"><path d="M408 442h480a8 8 0 0 0 8-8v-56a8 8 0 0 0-8-8H408a8 8 0 0 0-8 8v56a8 8 0 0 0 8 8zm-8 204a8 8 0 0 0 8 8h480a8 8 0 0 0 8-8v-56a8 8 0 0 0-8-8H408a8 8 0 0 0-8 8v56zm504-486H120a8 8 0 0 0-8 8v56a8 8 0 0 0 8 8h784a8 8 0 0 0 8-8v-56a8 8 0 0 0-8-8zm0 632H120a8 8 0 0 0-8 8v56a8 8 0 0 0 8 8h784a8 8 0 0 0 8-8v-56a8 8 0 0 0-8-8zM142.4 642.1L298.7 519a8.8 8.8 0 0 0 0-13.9L142.4 381.9a8.9 8.9 0 0 0-14.4 6.9v246.3a8.9 8.9 0 0 0 14.4 7z"></path></svg>
      </button>
      <!-- Хлебные крошки -->
      <nav class="breadcrumbs">
        <span v-for="(crumb, i) in breadcrumbs" :key="i">
          <span v-if="i !== 0">/</span>
          {{ crumb }}
        </span>
      </nav>
      <!-- Кнопка настроек -->
      <button class="icon-btn" @click="showSettings = true" title="Настройки">
        <svg viewBox="0 0 24 24" width="20" height="20"><path d="M12 15.5A3.5 3.5 0 1 0 12 8.5a3.5 3.5 0 0 0 0 7Zm8.94-2.06-.82-.14a7.18 7.18 0 0 0-.46-1.12l.52-.64a1 1 0 0 0-.08-1.32l-1.42-1.42a1 1 0 0 0-1.32-.08l-.64.52a7.18 7.18 0 0 0-1.12-.46l-.14-.82a1 1 0 0 0-1-.82h-2a1 1 0 0 0-1 .82l-.14.82a7.18 7.18 0 0 0-1.12.46l-.64-.52a1 1 0 0 0-1.32.08l-1.42 1.42a1 1 0 0 0-.08 1.32l.52.64a7.18 7.18 0 0 0-.46 1.12l-.82.14a1 1 0 0 0-.82 1v2a1 1 0 0 0 .82 1l.82.14a7.18 7.18 0 0 0 .46 1.12l-.52.64a1 1 0 0 0 .08 1.32l1.42 1.42a1 1 0 0 0 1.32.08l.64-.52a7.18 7.18 0 0 0 1.12.46l.14.82a1 1 0 0 0 1 .82h2a1 1 0 0 0 1-.82l.14-.82a7.18 7.18 0 0 0 1.12-.46l.64.52a1 1 0 0 0 1.32-.08l1.42-1.42a1 1 0 0 0 .08-1.32l-.52-.64a7.18 7.18 0 0 0 .46-1.12l.82-.14a1 1 0 0 0 .82-1v-2a1 1 0 0 0-.82-1Z"></path></svg>
      </button>
      <button class="logout-btn" @click="logout">Выйти</button>
    </header>

    <!-- Боковое меню -->
    <aside v-show="sidebarOpen" class="admin-sidebar">
      <nav>
        <ul>
            <li v-for="item in menuItems" :key="item.path">
                <template v-if="item.children">
                    <div class="submenu-label" @click="toggleSubmenu(item.path)">
                        <span v-if="item.icon" class="menu-icon"><component :is="item.icon" /></span>{{ item.label }}
                    </div>
                <ul class="submenu" v-show="openSubmenus[item.path]">
                <li v-for="child in item.children" :key="child.path">
                    <a href="#" :class="{active: activeTab.path === child.path}" @click.prevent="openTab(child)">
                    → {{ child.label }}
                    </a>
                </li>
                </ul>
            </template>
            <template v-else>
                <a href="#" :class="{active: activeTab.path === item.path}" @click.prevent="openTab(item)">
               <span v-if="item.icon" class="menu-icon"><component :is="item.icon" /></span>{{ item.label }}
                </a>
            </template>
            </li>
        </ul>
      </nav>
    </aside>

    <!-- Окна-вкладки -->
    <main class="admin-content">
        <div v-if="tabsMode" class="tabs-container">
            <div class="tab-list">
            <div v-for="tab in tabs" :key="tab.path" :class="['tab', {active: tab.path === activeTab.path}]" @click="switchTab(tab)">
                <span>{{ tab.label }}</span>
                <button v-if="tabs.length > 1" class="close-tab" @click.stop="closeTab(tab)">×</button>
            </div>
            </div>
            <div class="tab-content">
            <component :is="activeTab.component" />
            </div>
        </div>

        <!-- 👇 Классический режим — через router-view -->
        <div v-else class="tab-content">
            <router-view />
        </div>
        </main>

    <!-- Настройки -->
    <el-dialog v-model="showSettings" title="Настройки" width="350px">
      <el-switch v-model="tabsMode" active-text="Режим вкладок (окна)" inactive-text="Классический режим" />
      <br />
      <el-button type="primary" @click="showSettings = false" style="margin-top: 16px;">OK</el-button>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, computed, markRaw, provide } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { h } from 'vue'
import { ElIcon } from 'element-plus'
import {
  Odometer,
  Grid,
  Operation,
  View,
  Document
} from '@element-plus/icons-vue'
import DashboardIndex from './admin/DashboardIndex.vue'
import HomeGrid from './admin/HomeGrid.vue'
import Projects from './admin/Projects.vue'
import CreateProject from './admin/CreateProject.vue' // 👈 ДОБАВИЛ ЭТО
import Workflow from './admin/Workflow.vue'
import Services from './admin/Services.vue'
import axios from 'axios'


const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(true)
const showSettings = ref(false)
const tabsMode = ref(true)
const openSubmenus = ref({})

// 👇 Обновлённое меню с вложенным блоком "Проекты"
const menuItems = [
  {
    path: '/dashboard',
    label: 'Панель управления',
    icon: () => h(ElIcon, null, () => h(Odometer)),
    component: markRaw(DashboardIndex)
  },
  {
    path: '/home-grid',
    label: 'Home Grid',
    icon: () => h(ElIcon, null, () => h(Grid)),
    component: markRaw(HomeGrid)
  },
  {
    path: '/projects',
    label: 'Проекты',
    icon: () => h(ElIcon, null, () => h(Document)),
    children: [
      {
        path: '/projects/create',
        label: 'Создать проект',
        component: markRaw(CreateProject)
      },
      {
        path: '/projects/list',
        label: 'Список проектов',
        component: markRaw(Projects)
      }
    ]
  },
  {
    path: '/workflow',
    label: 'Workflow',
    icon: () => h(ElIcon, null, () => h(View)),
    component: markRaw(Workflow)
  },
  {
    path: '/services',
    label: 'Услуги',
    icon: () => h(ElIcon, null, () => h(Operation)),
    component: markRaw(Services)
  }
]

const tabs = ref([
  { ...menuItems[0] }
])

const activeTab = ref(tabs.value[0])

const breadcrumbs = computed(() => {
  const tab = activeTab.value
  if (!tab) return []
  return ['Админка', tab.label]
})

function openTab(item) {
  let tab = tabs.value.find(t => t.path === item.path)
  if (!tab) {
    tab = { ...item }
    tabs.value.push(tab)
  } else if (item.label) {
    // Обновляем label если нужно
    tab.label = item.label
  }
  activeTab.value = tab
  if (!tabsMode.value) {
    router.push(item.path)
  }
}

function switchTab(tab) {
  activeTab.value = tab
  router.push(tab.path)
}
function closeTab(tab) {
  const idx = tabs.value.indexOf(tab)
  if (idx !== -1) {
    tabs.value.splice(idx, 1)
    if (activeTab.value === tab) {
      activeTab.value = tabs.value[idx] || tabs.value[idx - 1] || tabs.value[0]
      router.push(activeTab.value.path)
    }
  }
}
function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
}
function toggleSubmenu(path) {
  openSubmenus.value[path] = !openSubmenus.value[path]
}
router.afterEach((to) => {
  const flatItems = menuItems.flatMap(item => item.children ?? [item])
  const item = flatItems.find(m => m.path === to.path)

  if (item) {
    openTab(item)
  } else if (to.name === 'EditProject') {
    // Открываем вкладку вручную с заголовком
    openTab({
      path: to.path,
      label: 'Редактирование проекта',
      component: markRaw(CreateProject)
    })
  }
})

provide('openTab', openTab)
provide('tabsMode', tabsMode)

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
  flex-direction: row;
}
.admin-layout.sidebar-collapsed .admin-sidebar {
  width: 60px !important;
  min-width: 60px !important;
  transition: width .2s;
}
.admin-header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 56px;
  background: rgb(71, 71, 71);
  color: #fff;
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 100;
  padding: 0 20px;
}
.icon-btn {
  border: none;
  background: none;
  color: #fff;
  cursor: pointer;
  padding: 4px;
  margin-right: 12px;
  font-size: 20px;
}
.breadcrumbs {
  flex: 1;
  font-size: 15px;
  color: #f6ffe3;
  font-weight: 500;
}
.admin-sidebar {
  position: fixed;
  top: 56px;
  left: 0;
  width: 220px;
  min-width: 220px;
  height: calc(100vh - 56px);
  background: rgb(48, 65, 86);
  color: #e7ffe6;
  padding: 20px 0;
  z-index: 99;
  transition: width .2s;
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
  color:rgb(154, 184, 248);
  text-decoration: none;
  padding: 12px 28px;
  display: flex;
  align-items: center;
  border-radius: 6px;
  font-size: 17px;
  transition: background .13s;
}
.admin-sidebar a.active,
.admin-sidebar a:hover {
  background:rgb(48, 65, 86);
  color: #fff;
}
.admin-content {
  margin-left: 220px;
  margin-top: 56px;
  padding: 24px 26px 32px 26px;
  background: #f4f7f3;
  width: calc(100% - 220px);
  overflow-y: auto;
  min-height: 100vh;
  transition: margin-left .2s, width .2s;
}
.admin-layout.sidebar-collapsed .admin-content {
  margin-left: 60px;
  width: calc(100% - 60px);
}
.tabs-container {
  background: #dbf5e4;
  border-radius: 14px;
  padding: 0 0 10px 0;
  box-shadow: 0 2px 20px #d1f4c77e;
}
.tab-list {
  display: flex;
  gap: 6px;
  padding: 10px 10px 0 10px;
  min-height: 40px;
}
.tab {
  background: rgb(95, 111, 131);
  color: #fff;
  border-radius: 8px 8px 0 0;
  padding: 9px 22px 6px 16px;
  font-weight: 500;
  font-size: 16px;
  margin-right: 3px;
  position: relative;
  cursor: pointer;
  display: flex;
  align-items: center;
}
.tab.active {
  background: rgb(48, 65, 86);
  color: #fff;
}
.close-tab {
  background: none;
  border: none;
  color: #eee;
  margin-left: 10px;
  font-size: 15px;
  cursor: pointer;
}
.tab-content {
  background: #fff;
  border-radius: 0 0 12px 12px;
  box-shadow: 0 2px 8px #aadca56e;
  padding: 22px 22px 12px 22px;
  min-height: 320px;
}
.logout-btn {
  margin-left: 22px;
  background: #e74c3c;
  color: white;
  border: none;
  padding: 7px 18px;
  cursor: pointer;
  border-radius: 5px;
  font-size: 15px;
}

.submenu {
  padding-left: 10px;
  margin-top: 4px;
}
.submenu a {
  font-size: 15px;
  padding: 8px 36px;
}
.submenu-label {
  font-weight: bold;
  color: #aac9ff;
  margin-left: 28px;
}
.menu-icon {
  display: inline-flex;
  margin-right: 8px;
  align-items: center;
}
</style>
