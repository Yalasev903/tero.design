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
      <!-- Кнопка: на весь экран -->
        <button class="icon-btn" @click="toggleFullScreen" v-if="!isFullScreen" title="Развернуть на весь экран">
        <svg viewBox="0 0 128 128" width="20" height="20"><path d="M38.5 52L52 38.5 28.4 14.8 43.2 0H0v43.1l14.8-14.8L38.5 52zm74.7 47.7L89.5 76 76 89.5l23.6 23.7L84.8 128H128V84.9l-14.8 14.8zM89.5 52l23.7-23.6L128 43.2V0H84.9l14.8 14.8L76 38.5 89.5 52zm-51 24L14.8 99.7 0 84.7V128h43.1l-14.8-14.8L52 89.5 38.5 76z"></path></svg>
        </button>

        <!-- Кнопка: выйти из полного экрана -->
        <button class="icon-btn" @click="toggleFullScreen" v-else title="Выйти из полноэкранного режима">
        <svg viewBox="0 0 128 128" width="20" height="20"><path d="M49.2 41.3l-.1-35.2c0-2.7-2.3-4.4-5-4.4h-3.7a4.8 4.8 0 0 0-4.8 5l.2 19.2L11.6 2a6.7 6.7 0 0 0-9.5 0 6.8 6.8 0 0 0 0 9.5l24 23.7H7.6A5.5 5.5 0 0 0 2 40.5V44c0 2.7 2.3 5 5 5l35-.2h2.6a4.6 4.6 0 0 0 3.4-1.3c1-.9 1.2-2.1 1.2-3.5l-.3-2.4.2-.2zm52.5 51.2h18.4c2.7 0 5.2-1.6 5.6-4.8v-3.5c0-2.7-2.3-5-5-5l-34.6.2H86l-2.5-.1a4.6 4.6 0 0 0-3.4 1.4c-1 .8-1.2 2-1.2 3.4l.3 2.5-.2.1.1 34.7c0 2.7 2.3 4.4 5 4.4h3.5c2.7 0 4.9-2.3 4.8-5l-.2-18.8 24.2 24a6.7 6.7 0 0 0 9.5 0 6.7 6.7 0 0 0 0-9.5l-24.2-24z"></path></svg>
        </button>
        <!-- Кнопка выбора размера текста -->
    <el-dropdown @command="setInputSize" trigger="click">
    <el-button class="icon-btn" title="Размер текста">
        <svg version="1.1" viewBox="0 0 128 128" width="20" height="20">
        <path d="M0 54.9h54.8V73H36.5v55H18.3V73.1H0V55zm127.9-36.6h-36V128H72.5V18.3h-36V0H128v18.3z" />
        </svg>
    </el-button>
    <template #dropdown>
        <el-dropdown-menu>
        <el-dropdown-item command="default">Default</el-dropdown-item>
        <el-dropdown-item command="medium">Medium</el-dropdown-item>
        <el-dropdown-item command="small">Small</el-dropdown-item>
        <el-dropdown-item command="mini">Mini</el-dropdown-item>
        </el-dropdown-menu>
    </template>
    </el-dropdown>

    <el-button class="logout-btn" type="danger" @click="cleanupFiles">Очистить неиспользуемые файлы</el-button>

      <button class="logout-btn" @click="logout">Выйти</button>
    </header>
      <!-- Кнопка настроек -->
    <el-button
    class="floating-settings-button"
    type="primary"
    size="large"
    @click="showSettings = true"
    circle
    title="Настройки"
    >
    <el-icon class="icon-large"><Setting /></el-icon>
    </el-button>
    <!-- Боковое меню -->
    <aside class="admin-sidebar" :class="{ collapsed: !sidebarOpen }" ref="sidebarRef">
    <nav>
        <ul>
        <li v-for="item in menuItems" :key="item.path">
            <template v-if="item.children">
                <div class="submenu-wrapper">
                <a href="#"
                    class="submenu-label"
                    :data-submenu="item.path"
                    :class="{ 'submenu-open': openSubmenus[item.path] }"
                    @click.prevent="toggleSubmenu(item.path)">
                    <span v-if="item.icon" class="menu-icon"><component :is="item.icon" /></span>
                    <span class="menu-text">{{ item.label }}</span>
                </a>

                <!-- Обычное подменю -->
                <ul class="submenu" v-show="openSubmenus[item.path] && sidebarOpen">
                    <li v-for="child in item.children" :key="child.path">
                    <a href="#" :class="{ active: activeTab.path === child.path }" @click.prevent="openTab(child)">
                        <span class="menu-text">→ {{ child.label }}</span>
                    </a>
                    </li>
                </ul>

                <!-- Всплывающее подменю -->
                <Teleport to="body">
                    <ul
                        class="submenu-popup"
                        v-if="openSubmenus[item.path] && !sidebarOpen"
                        :style="getPopupStyle(null, item.path)"
                        >
                        <li v-for="child in item.children" :key="child.path">
                            <a href="#" :class="{ active: activeTab.path === child.path }" @click.prevent="selectChild(item.path, child)">
                            {{ child.label }}
                            </a>
                        </li>
                        </ul>
                </Teleport>
                </div>
            </template>

            <template v-else>
            <a href="#" :class="{ active: activeTab.path === item.path }" @click.prevent="openTab(item)">
                <span v-if="item.icon" class="menu-icon"><component :is="item.icon" /></span>
                <span class="menu-text">{{ item.label }}</span>
            </a>
            </template>
        </li>
        </ul>
    </nav>
    </aside>

    <!-- Окна-вкладки -->
    <main class="admin-content">
        <div v-if="tabsMode" class="tabs-container">
<draggable
  v-model="tabs"
  item-key="path"
  class="tab-list"
  :animation="200"
  :ghost-class="'dragging-tab'"
  @end="onTabsReorder"
>
  <template #item="{ element: tab }">
    <div :class="['tab', { active: tab.path === activeTab.path }]" @click="switchTab(tab)">
      <span>{{ tab.label }}</span>
      <button
        v-if="tabs.length > 1"
        class="close-tab"
        @click.stop="closeTab(tab)"
      >×</button>
    </div>
  </template>
</draggable>
            <div class="tab-content">
            <!-- <component :is="activeTab.component" /> -->
                <keep-alive :include="cachedComponentNames">
                <component
                    :is="activeTab.component"
                    :key="activeTab.key || generateTabKey(activeTab)"
                    />
                </keep-alive>
            </div>
        </div>

        <!-- 👇 Классический режим — через router-view -->
        <div v-else class="tab-content">
            <router-view />
        </div>
        </main>

    <!-- Настройки -->
<el-dialog v-model="showSettings" title="Настройки" width="400px">
  <!-- Переключатель режима -->
  <el-switch
    v-model="tabsMode"
    active-text="Режим вкладок (окна)"
    inactive-text="Классический режим"
  />
  <br /><br />

  <!-- Блок смены пароля -->
  <el-divider>Смена пароля</el-divider>
  <el-form :model="passwordForm" label-position="top" @submit.prevent="changePassword" :size="inputSize" >
    <el-form-item label="Новый пароль">
      <el-input v-model="passwordForm.password" type="password" show-password  :size="inputSize" />
    </el-form-item>
    <el-form-item label="Подтвердите пароль">
      <el-input v-model="passwordForm.password_confirmation" type="password" show-password  :size="inputSize" />
    </el-form-item>
    <el-button type="primary" @click="changePassword">Сменить пароль</el-button>
  </el-form>

  <!-- Сообщения -->
  <el-alert
    v-if="passwordSuccess"
    type="success"
    :closable="false"
    class="mt-4"
    title="Пароль успешно обновлён"
  />
  <el-alert
    v-if="passwordError"
    type="error"
    :closable="false"
    class="mt-4"
    :title="passwordError"
  />

  <!-- Закрытие -->
  <template #footer>
    <el-button @click="showSettings = false">Закрыть</el-button>
  </template>
</el-dialog>

  </div>
</template>

<script setup>
import { ref, computed, markRaw, provide, onMounted, onBeforeUnmount, inject, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { h } from 'vue'
import { ElIcon } from 'element-plus'
import {
  Odometer,
  Grid,
  Operation,
  View,
  Document,
  Setting
} from '@element-plus/icons-vue'
import { ElDivider, ElAlert, ElMessageBox, ElMessage } from 'element-plus'
import draggable from 'vuedraggable'
import DashboardIndex from './admin/DashboardIndex.vue'
import HomeGrid from './admin/HomeGrid.vue'
import Projects from './admin/Projects.vue'
import CreateProject from './admin/CreateProject.vue' // 👈 ДОБАВИЛ ЭТО
import Workflow from './admin/Workflow.vue'
import Services from './admin/Services.vue'
import axios from 'axios'


const router = useRouter()
const route = useRoute()

const sidebarOpen = ref(localStorage.getItem('sidebarOpen') !== 'false')
const showSettings = ref(false)
const tabsMode = ref(true)
const openSubmenus = ref({})

const cachedComponentNames = ['Projects', 'HomeGrid', 'SeoIndex', 'SeoProject', 'SeoServices', 'ShowreelPreview', 'DashboardIndex', 'Workflow', 'Services', 'CreateProject']

const isFullScreen = ref(false)

const sidebarRef = ref(null)

const cleanupFiles = async () => {
  console.log('НАЖАЛИ НА КНОПКУ')
  try {
    // Шаг 1: Предпросмотр
    const preview = await axios.post('/api/admin/cleanup', { preview: true })

    const totalFiles = preview.data.total_files
    const unusedFiles = preview.data.unused_files_count
    const totalDirs = preview.data.total_dirs
    const emptyDirs = preview.data.empty_dirs_count

    if (unusedFiles === 0 && emptyDirs === 0) {
      ElMessage.success('Нет неиспользуемых файлов и пустых папок')
      return
    }

    const confirm = await ElMessageBox.confirm(
      `Всего файлов: ${totalFiles}\n` +
      `Неиспользуемых файлов: ${unusedFiles}\n\n` +
      `Всего папок: ${totalDirs}\n` +
      `Пустых папок: ${emptyDirs}\n\n` +
      `Удалить ${unusedFiles} файлов и ${emptyDirs} папок?`,
      'Подтверждение очистки',
      {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        type: 'warning',
        dangerouslyUseHTMLString: false
      }
    )

    // Шаг 2: Удаление после подтверждения
    const { data } = await axios.post('/api/admin/cleanup', { preview: false })

    ElMessage.success(
      `Удалено файлов: ${data.deleted_files}, папок: ${data.deleted_dirs}`
    )
  } catch (err) {
    if (err !== 'cancel') {
      ElMessage.error('Ошибка при очистке')
    }
  }
}

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

function onTabsReorder(event) {
  localStorage.setItem('admin-tabs', JSON.stringify(tabs.value))
}

const tabs = ref([
  { ...menuItems[0] }
])

const activeTab = ref(tabs.value[0])

const breadcrumbs = computed(() => {
  const tab = activeTab.value
  if (!tab) return []
  return ['Админка', tab.label]
})

function switchTab(tab) {
  activeTab.value = tab
  localStorage.setItem('active-tab-path', tab.path)
  router.push(tab.path)

  setTimeout(() => {
    const videos = document.querySelectorAll('video[autoplay]')
    videos.forEach((video) => {
      if (video.paused) {
        video.play().catch(() => {})
      }
    })
  }, 300)
}

function generateTabKey(tab) {
  return tab.key || tab.path
}

function openTab(item) {
  const isDynamic = item.path.startsWith('/projects/edit/') || item.path === '/projects/create'

  // Стабильный key для кеша и <keep-alive>
  item.key = isDynamic ? `${item.path}-${Date.now()}` : item.path

  let tab

  if (isDynamic) {
    // Для dynamic — всегда новый tab
    tab = { ...item }
    tabs.value.push(tab)
  } else {
    // Для обычных путей — не создавать дубли
    tab = tabs.value.find(t => t.path === item.path)
    if (!tab) {
      tab = { ...item }
      tabs.value.push(tab)
    }
  }

  activeTab.value = tab
  localStorage.setItem('admin-tabs', JSON.stringify(tabs.value))
  localStorage.setItem('active-tab-path', tab.path)

  if (!tabsMode.value) {
    router.push(item.path)
  }
}

function closeTab(tab) {
  const idx = tabs.value.indexOf(tab)
  if (idx !== -1) {
    if (tab.path === '/projects/create') {
      localStorage.removeItem('was-create-opened')
      localStorage.removeItem('create-project-form')
    }

    tabs.value.splice(idx, 1)

    localStorage.setItem('admin-tabs', JSON.stringify(tabs.value))

    if (activeTab.value === tab) {
      activeTab.value = tabs.value[idx] || tabs.value[idx - 1] || tabs.value[0]
      localStorage.setItem('active-tab-path', activeTab.value.path)
      router.push(activeTab.value.path)
    }
  }
}

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value
  localStorage.setItem('sidebarOpen', sidebarOpen.value)
}
function toggleSubmenu(path) {
  openSubmenus.value[path] = !openSubmenus.value[path]
}

router.afterEach((to) => {
  // Пропускаем системные маршруты
  if (to.path === '/login') return

  const flatItems = menuItems.flatMap(item => item.children ?? [item])
  const existingTab = tabs.value.find(t => t.path === to.path)

  // Если таб уже есть, не нужно заново открывать
  if (existingTab) {
    activeTab.value = existingTab
    localStorage.setItem('active-tab-path', to.path)
    return
  }

  const item = flatItems.find(m => m.path === to.path)

  if (item) {
    openTab(item)
  } else if (to.name === 'EditProject') {
    openTab({
      path: to.path,
      label: 'Редактирование проекта',
      component: markRaw(CreateProject)
    })
  }
})

const inputSize = ref('default') // по умолчанию
function setInputSize(size) {
  inputSize.value = size
  localStorage.setItem('input-size', size)
}


provide('openTab', openTab)
provide('tabsMode', tabsMode)
provide('inputSize', inputSize)


const logout = async () => {
  await axios.post('/api/admin/logout')
  localStorage.removeItem('admin-tabs')
  localStorage.removeItem('active-tab-path')
  router.push('/login')
}

const passwordForm = ref({
  password: '',
  password_confirmation: ''
})

const passwordSuccess = ref(false)
const passwordError = ref('')

const changePassword = async () => {
  passwordSuccess.value = false
  passwordError.value = ''
  try {
    await axios.post('/api/admin/change-password', passwordForm.value)
    passwordSuccess.value = true
    passwordForm.value.password = ''
    passwordForm.value.password_confirmation = ''
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Ошибка смены пароля'
  }
}

function selectChild(parentPath, item) {
  openTab(item)
  openSubmenus.value[parentPath] = false
}

const popupPositions = ref({})

function getPopupStyle(event, path) {
  const rect = sidebarRef.value?.querySelector(`[data-submenu="${path}"]`)?.getBoundingClientRect()
  if (!rect) return {}
  return {
    position: 'fixed',
    top: `${rect.top}px`,
    left: `${rect.right}px`,
    zIndex: 9999,
    background: 'rgb(48, 65, 86)',
    borderRadius: '6px',
    padding: '8px 0',
    minWidth: '160px',
    boxShadow: '0 2px 10px rgba(0,0,0,0.2)'
  }
}

function toggleFullScreen() {
  const el = document.documentElement

  if (!document.fullscreenElement) {
    el.requestFullscreen?.()
      .then(() => {
        isFullScreen.value = true
      })
      .catch((err) => {
        console.error('Не удалось войти в полноэкранный режим:', err)
      })
  } else {
    document.exitFullscreen?.()
      .then(() => {
        isFullScreen.value = false
      })
      .catch((err) => {
        console.error('Не удалось выйти из полноэкранного режима:', err)
      })
  }
}

onMounted(() => {
  const savedSize = localStorage.getItem('input-size')
  if (savedSize) inputSize.value = savedSize

  const flatItems = menuItems.flatMap(item => item.children ?? [item])
  const tabComponentsMap = {
    '/dashboard': DashboardIndex,
    '/home-grid': HomeGrid,
    '/projects/create': CreateProject,
    '/projects/list': Projects,
    '/workflow': Workflow,
    '/services': Services,
    '/seo/index': SeoIndex,
    '/seo/project': SeoProject,
    '/seo/services': SeoServices,
    '/showreel': ShowreelPreview
  }

  // 1. Восстановление порядка вкладок
  const savedTabs = localStorage.getItem('admin-tabs')
  if (savedTabs) {
    try {
      const parsedTabs = JSON.parse(savedTabs)
      tabs.value = parsedTabs.map(savedTab => {
        const path = savedTab.path
        let component = tabComponentsMap[path] || null

        if (!component && path.startsWith('/projects/edit/')) {
          component = CreateProject
        }

        return {
          ...savedTab,
          component: component ? markRaw(component) : undefined,
          key: savedTab.key || path
        }
      })
    } catch (e) {
      console.error('Ошибка парсинга сохранённых вкладок:', e)
    }
  }

  // 2. Восстановление активной вкладки
  const savedPath = localStorage.getItem('active-tab-path')
  if (savedPath) {
    const restoredTab =
      tabs.value.find(t => t.path === savedPath) ||
      flatItems.find(m => m.path === savedPath)

    if (restoredTab) {
      openTab(restoredTab)
      router.push(savedPath)
    }
  }

  // 3. Закрытие всплывающих меню при клике вне
  document.addEventListener('click', handleClickOutside)

  // 4. Слежение за полноэкранным режимом
  document.addEventListener('fullscreenchange', () => {
    isFullScreen.value = !!document.fullscreenElement
  })
})

function refreshCurrentTab() {
  const current = activeTab.value
  if (current && current.path === '/projects/create') {
    localStorage.removeItem('create-project-form') // или любой ключ, если используешь сохранение формы
    router.replace({ path: current.path + '?refresh=' + Date.now() })
  }
}

// Закрытие всплывающих меню при клике вне
function handleClickOutside(event) {
  if (!sidebarRef.value?.contains(event.target)) {
    Object.keys(openSubmenus.value).forEach(key => {
      openSubmenus.value[key] = false
    })
  }
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
.admin-sidebar.collapsed {
  width: 60px !important;
  min-width: 60px !important;
  overflow: hidden;
  padding-left: 6px;
}

/* Скрыть подписи в любом пункте меню */
.admin-sidebar.collapsed .menu-text {
  display: none;
}

/* Центровать всё, что осталось (иконки) */
.admin-sidebar.collapsed a,
.admin-sidebar.collapsed .submenu-label {
  justify-content: center;
  padding: 12px;
}

/* Скрыть подменю (вложенные пункты) */
.admin-sidebar.collapsed .submenu {
  display: none;
}


/* Центрируем и убираем отступ при сворачивании */
.admin-sidebar.collapsed .submenu-label {
  justify-content: center;
  padding: 12px;
  margin-left: 0 !important;
}
.submenu-label {
  font-weight: bold;
  color: #aac9ff;
  display: flex;
  align-items: center;
  padding: 12px 28px;
  border-radius: 6px;
  transition: background .13s;
  margin-left: 0;
}



/* Центрируем и убираем отступ при сворачивании */
.admin-sidebar.collapsed .submenu-popup {
  left: 60px; /* отступ от иконки */
}

.submenu-wrapper {
  position: relative;
}

.submenu-popup {
  position: absolute;
  top: 0;
  left: 100%;
  background: rgb(48, 65, 86);
  border-radius: 6px;
  padding: 8px 0;
  z-index: 9999;
  min-width: 160px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.submenu-popup li a {
  color: rgb(154, 184, 248);
  padding: 10px 20px;
  display: block;
  white-space: nowrap;
  transition: background 0.2s;
}

.submenu-popup li a:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}
.submenu-popup::before {
  content: "";
  position: absolute;
  top: 12px;
  left: -6px;
  width: 0;
  height: 0;
  border-top: 6px solid transparent;
  border-bottom: 6px solid transparent;
  border-right: 6px solid rgb(48, 65, 86);
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
.dragging-tab {
  opacity: 0.6;
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
.menu-icon {
  display: inline-flex;
  margin-right: 8px;
  align-items: center;
}
.floating-settings-button {
  position: fixed;
  top: 50%;
  right: 24px;
  transform: translateY(-50%);
  z-index: 1000;
  width: 56px;
  height: 56px;
  padding: 0;
  border-radius: 14px !important;
  background-color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
.floating-settings-button:hover {
  background-color: #3b82f6;
}
.floating-settings-button .icon-large {
  font-size: 26px;
  color: white;
}
.floating-center-button:hover {
  background: #3b82f6;
}

</style>
