<template>
  <div class="page-wrap">
    <header class="header">
      <h1>Услуги</h1>
      <RouterLink to="/services/new" class="button">Новая услуга</RouterLink>
      <el-button type="success" @click="saveServices" :loading="saving" class="save-btn">
        <el-icon><Check /></el-icon> Сохранить порядок
      </el-button>
    </header>

    <section v-if="services.length">
      <draggable v-model="services" group="services" animation="200" handle=".drag-handle" item-key="col_id">
        <template #item="{ element: service, index }">
          <div class="service-item">
            <span class="drag-handle" title="Перетащить">
              <el-icon><Menu /></el-icon>
            </span>
            <div class="service-info">
              <div><b>ID:</b> {{ service.col_id }}</div>
              <div><b>Название:</b> {{ service.col_title }}</div>
            </div>
            <div class="service-video" @click="openPreview(service.col_video)">
              <video
                v-if="service.col_video"
                autoplay
                loop
                muted
                playsinline
                style="max-width: 180px; max-height: 120px; cursor: pointer;"
              >
                <source :src="videoUrl(service.col_video)" type="video/mp4" />
                Ваш браузер не поддерживает видео.
              </video>
              <div v-else class="no-video">Нет видео</div>
            </div>
            <div class="service-actions">
              <RouterLink
                :to="`/services/${service.col_id}/edit`"
                class="icon2"
                title="Редактировать"
              >✏️</RouterLink>
              <a href="#" class="icon2" title="Удалить" @click.prevent="deleteService(service.col_id)">🗑</a>
            </div>
          </div>
        </template>
      </draggable>
    </section>

    <p v-else>Нет данных</p>

    <!-- Предпросмотр видео -->
    <el-dialog
      :visible.sync="previewVisible"
      title="Предпросмотр видео"
      width="50%"
      :before-close="() => { previewVisible = false }"
    >
      <video
        v-if="previewVideo"
        controls
        autoplay
        loop
        muted
        playsinline
        style="width: 100%;"
      >
        <source :src="previewVideo" type="video/mp4" />
        Ваш браузер не поддерживает видео.
      </video>
      <p v-else>Видео отсутствует</p>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import { ElNotification, ElMessageBox } from 'element-plus'
import { Menu, Check } from '@element-plus/icons-vue'

const services = ref([])
const saving = ref(false)

const previewVisible = ref(false)
const previewVideo = ref('')

const fetchServices = async () => {
  try {
    const res = await axios.get('/api/admin/tbl-services')
    services.value = res.data
      .map(item => ({
        col_id: item.col_id,
        col_title: item.col_title,
        col_description: item.col_description,
        col_video: item.col_video,
        position: item.position || 0, // если есть поле position
      }))
      .sort((a, b) => (a.position || 0) - (b.position || 0))
  } catch (e) {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось загрузить услуги',
      type: 'error',
    })
  }
}

const saveServices = async () => {
  saving.value = true
  try {
    const payload = services.value.map((s, i) => ({
      col_id: s.col_id,
      position: i + 1,
    }))
    await axios.post('/api/admin/tbl-services/reorder', { services: payload })
    ElNotification({
      title: 'Успешно',
      message: 'Порядок сохранен',
      type: 'success',
    })
    await fetchServices()
  } catch (e) {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось сохранить порядок',
      type: 'error',
    })
  }
  saving.value = false
}

const deleteService = async (id) => {
  try {
    await ElMessageBox.confirm('Удалить услугу?', 'Внимание', {
      confirmButtonText: 'Удалить',
      cancelButtonText: 'Отмена',
      type: 'warning',
    })
    await axios.delete(`/api/admin/tbl-services/${id}`)
    await fetchServices()
    ElNotification({
      title: 'Успешно',
      message: 'Услуга удалена',
      type: 'success',
    })
  } catch (e) {
    if (e !== 'cancel') {
      ElNotification({
        title: 'Ошибка',
        message: 'Не удалось удалить услугу',
        type: 'error',
      })
    }
  }
}

const videoUrl = (video) => {
  if (!video) return ''
  return `/multimedia/${video}`
}

const openPreview = (video) => {
  const url = videoUrl(video)
  if (!url) return
  previewVideo.value = url
  previewVisible.value = true
}

onMounted(() => {
  fetchServices()
})
</script>

<style scoped>
.page-wrap {
  padding: 20px;
  max-width: 900px;
  margin: auto;
}
.header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
}
.button {
  background: #409eff;
  color: white;
  padding: 6px 12px;
  text-decoration: none;
  border-radius: 4px;
  font-weight: 600;
}
.save-btn {
  margin-left: auto;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.service-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 10px;
  background: #fafafa;
  user-select: none;
}
.drag-handle {
  cursor: grab;
  color: #999;
  font-size: 20px;
}
.service-info {
  flex-grow: 1;
  min-width: 150px;
}
.service-video {
  width: 180px;
  height: 120px;
  position: relative;
  cursor: pointer;
}
.no-video {
  width: 180px;
  height: 120px;
  background: #eee;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #888;
  font-style: italic;
}
.service-actions {
  display: flex;
  gap: 10px;
  font-size: 22px;
  user-select: none;
}
.icon2 {
  cursor: pointer;
  color: #555;
}
.icon2:hover {
  color: #409eff;
}
</style>
