<template>
  <div class="page-wrap">
    <header class="header">
      <h1>Услуги</h1>
      <el-button type="primary" @click="openAddModal">Добавить услугу</el-button>
      <el-button type="success" @click="saveServices" :loading="saving" class="save-btn">
        <el-icon><Check /></el-icon> Сохранить порядок
      </el-button>
    </header>

    <section v-if="services.length">
      <draggable
        v-model="services"
        group="services"
        animation="200"
        handle=".drag-handle"
        item-key="col_id"
      >
        <template #item="{ element: service }">
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
              <RouterLink :to="`/services/${service.col_id}/edit`" class="icon2" title="Редактировать">✏️</RouterLink>
              <a href="#" class="icon2" title="Удалить" @click.prevent="deleteService(service.col_id)">🗑</a>
            </div>
          </div>
        </template>
      </draggable>
    </section>

    <p v-else>Нет данных</p>

    <!-- Модальное окно добавления услуги -->
        <el-dialog
        v-model="addModalVisible"
        title="Добавить новую услугу"
        width="60%"
        :before-close="handleBeforeClose"
        destroy-on-close
        append-to-body
        >
      <el-form :model="newService" label-position="top" ref="addForm">
        <el-form-item label="Название услуги" required>
          <el-input v-model="newService.col_title" placeholder="Введите название услуги" />
        </el-form-item>

        <el-form-item label="Описание услуги">
          <textarea
            id="tinymce-editor"
            style="min-height: 200px"
            placeholder="Описание услуги (редактор выключен)"
          ></textarea>
        </el-form-item>

        <el-form-item label="Видео или изображение">
          <div class="media-preview" @click="openFileManager">
            <template v-if="mediaPreview.type === 'video'">
              <video
                autoplay
                loop
                muted
                playsinline
                style="max-width: 100%; max-height: 180px; border-radius: 8px; cursor: pointer;"
              >
                <source :src="mediaPreview.link" type="video/mp4" />
                Ваш браузер не поддерживает видео.
              </video>
            </template>
            <template v-else-if="mediaPreview.type === 'img'">
              <img
                :src="mediaPreview.link"
                alt="preview"
                style="max-width: 100%; max-height: 180px; border-radius: 8px; cursor: pointer;"
              />
            </template>
            <template v-else>
              <div class="no-media">Кликните для выбора видео или изображения</div>
            </template>
          </div>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="addModalVisible = false">Отмена</el-button>
        <el-button type="primary" :loading="adding" @click="submitNewService">Добавить</el-button>
      </template>
    </el-dialog>

    <!-- Модалка предпросмотра видео -->
    <el-dialog
      :visible.sync="previewVisible"
      title="Предпросмотр видео"
      width="50%"
      :before-close="() => { previewVisible.value = false }"
      append-to-body
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

    <!-- Файловый менеджер -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal">
        <div class="finder-homegrid">
          <vue-finder
            id="vuefinder"
            :request="{
              baseUrl: '/api/vuefinder',
              adapter: 'local',
              xsrfHeaderName: 'X-XSRF-TOKEN'
            }"
            @select="handleFileSelect"
          />
          <button class="close-btn" @click="showFileManager = false">✖</button>
        </div>
      </div>
    </teleport>
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

const addModalVisible = ref(false)
const adding = ref(false)

const newService = ref({
  col_title: '',
  col_description: '',
  col_video: ''
})

const mediaPreview = ref({
  type: '',
  link: ''
})

const showFileManager = ref(false)

const handleBeforeClose = () => {
  addModalVisible.value = false
  // destroyTinyMCE() можно включить при необходимости
}

const fetchServices = async () => {
  try {
    const res = await axios.get('/api/admin/tbl-services')
    services.value = res.data
      .map(item => ({
        col_id: item.col_id,
        col_title: item.col_title,
        col_description: item.col_description,
        col_video: item.col_video,
        position: item.position || 0,
      }))
      .sort((a, b) => (a.position || 0) - (b.position || 0))
  } catch {
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
    ElNotification({ title: 'Успешно', message: 'Порядок сохранен', type: 'success' })
    await fetchServices()
  } catch {
    ElNotification({ title: 'Ошибка', message: 'Не удалось сохранить порядок', type: 'error' })
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
    ElNotification({ title: 'Успешно', message: 'Услуга удалена', type: 'success' })
  } catch (e) {
    if (e !== 'cancel') {
      ElNotification({ title: 'Ошибка', message: 'Не удалось удалить услугу', type: 'error' })
    }
  }
}

const videoUrl = (video) => (video ? `/multimedia/${video}` : '')

const openPreview = (video) => {
  const url = videoUrl(video)
  if (!url) return
  previewVideo.value = url
  previewVisible.value = true
}

const openAddModal = () => {
  addModalVisible.value = true
  newService.value = { col_title: '', col_description: '', col_video: '' }
  mediaPreview.value = { type: '', link: '' }
  // Можно раскомментировать для TinyMCE
  // nextTick(() => initTinyMCE())
}

const submitNewService = () => {
  // Пока без TinyMCE, описание пустое
  // const editorContent = tinymce.get('tinymce-editor')?.getContent() || ''
  // newService.value.col_description = editorContent

  if (!newService.value.col_title.trim()) {
    ElNotification({ title: 'Ошибка', message: 'Название услуги обязательно', type: 'error' })
    return
  }

  adding.value = true
  axios.post('/api/admin/tbl-services', {
    col_title: newService.value.col_title,
    col_description: newService.value.col_description,
    col_video: mediaPreview.value.type === 'video' ? mediaPreview.value.link.replace(/^\/multimedia\//, '') : '',
  })
    .then(() => {
      ElNotification({ title: 'Успешно', message: 'Услуга добавлена', type: 'success' })
      addModalVisible.value = false
      fetchServices()
      // destroyTinyMCE()
    })
    .catch(() => {
      ElNotification({ title: 'Ошибка', message: 'Не удалось добавить услугу', type: 'error' })
    })
    .finally(() => {
      adding.value = false
    })
}

const openFileManager = () => {
  showFileManager.value = true
}

const handleFileSelect = (items) => {
  if (!items.length) return
  const file = items[0]
  let type = 'img'
  if (file.mime?.includes('video')) type = 'video'
  const path = file.path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  mediaPreview.value = { type, link: `/multimedia/${path}` }
  showFileManager.value = false
}

const destroyTinyMCE = () => {
  if (window.tinymce) window.tinymce.remove('#tinymce-editor')
}

onMounted(() => fetchServices())
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
  border-radius: 4px;
  font-weight: 600;
  cursor: pointer;
  border: none;
}
.save-btn {
  margin-left: auto;
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
.media-preview {
  border: 2px dashed #409eff;
  border-radius: 8px;
  height: 180px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  background: #f0f8ff;
  margin-top: 5px;
}
.no-media {
  color: #aaa;
  font-style: italic;
  user-select: none;
  padding: 0 10px;
}
.finder-modal {
  position: fixed;
  top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.finder-homegrid {
  width: 90%;
  height: 90%;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #e00;
  color: white;
  border: none;
  border-radius: 6px;
  padding: 5px 10px;
  cursor: pointer;
  z-index: 10;
}
</style>
