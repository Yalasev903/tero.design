<template>
  <div class="projects-list-page">
    <div class="header-section">
      <el-page-header content="Список проектов">
        <template #extra>
          <el-button type="primary" @click="createNewProject">
            <el-icon><Plus /></el-icon>
            Создать проект
          </el-button>
        </template>
      </el-page-header>
    </div>

    <el-table
      v-loading="loading"
      :data="projects"
      border
      size="large"
      stripe
      style="width: 100%; margin-top: 20px;"
    >
      <el-table-column label="ID" prop="id" width="80" sortable />
      <el-table-column label="Название" prop="title" width="220" sortable />
      <el-table-column label="Описание" min-width="500">
        <template #default="{ row }">
          <div class="text-truncate">{{ stripHtml(row.text1).slice(0, 250) }}</div>
        </template>
      </el-table-column>
      <el-table-column label="Действия" width="180" align="center">
        <template #default="{ row }">
          <el-button
            size="small"
            type="primary"
            @click="editProject(row.id)"
            title="Редактировать"
          >
            <el-icon><Edit /></el-icon>
          </el-button>
          <el-button
            size="small"
            type="danger"
            @click="deleteProject(row.id)"
            title="Удалить"
          >
            <el-icon><Delete /></el-icon>
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script setup>
defineOptions({
  name: 'Projects'
})
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { ElNotification, ElMessageBox } from 'element-plus'
import { Edit, Delete, Plus } from '@element-plus/icons-vue'

const projects = ref([])
const loading = ref(false)
const router = useRouter()

const fetchProjects = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/projects')
    projects.value = data
  } catch (e) {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось загрузить список проектов',
      type: 'error'
    })
  } finally {
    loading.value = false
  }
}

const createNewProject = () => {
  router.push('/projects/create')
}

const editProject = (id) => {
  router.push(`/projects/edit/${id}`)
}

const deleteProject = async (id) => {
  try {
    await ElMessageBox.confirm(
      'Вы уверены, что хотите удалить проект?',
      'Подтвердите удаление',
      {
        confirmButtonText: 'Да',
        cancelButtonText: 'Отмена',
        type: 'warning'
      }
    )

    await axios.delete(`/api/admin/projects/${id}`)
    await fetchProjects()

    ElNotification({
      title: 'Удалено',
      message: 'Проект успешно удалён',
      type: 'success'
    })
  } catch (err) {
    // отменено или ошибка
  }
}


const stripHtml = (html) => {
  const div = document.createElement('div')
  div.innerHTML = html || ''
  return div.textContent || div.innerText || ''
}

onMounted(fetchProjects)
</script>

<style scoped>
.projects-list-page {
  padding: 24px;
  background: #fff;
  border-radius: 10px;
}

.text-truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
  color: #333;
}
</style>
