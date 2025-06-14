<template>
  <div class="page-wrap">
                    <a href="/services" target="_blank" class="inline-flex items-center text-blue-600 hover:underline text-lg font-medium mb-4">
                    <el-icon class="mr-1"><House /></el-icon>
                    Открыть страницу
                    </a>
        <!-- SEO -->
        <SeoServices title="SEO страницы Услуги" pageName="services" />
    <header class="header">
      <el-button type="primary" @click="openAddModal"><el-icon><Plus /></el-icon>Добавить услугу</el-button>
      <el-button type="success" @click="saveServices" :loading="saving" class="save-btn">
        <el-icon><Check /></el-icon> Сохранить порядок
      </el-button>
    </header>

    <section v-if="services.length">
      <draggable
        v-model="services"
        group="services"
        animation="200"
        item-key="col_id"
        cancel=".service-actions, .service-actions *"
      >
        <template #item="{ element: service }">
          <div class="service-item" title="Перетащить">
            <span class="drag-handle" aria-hidden="true">
              <el-icon><Rank /></el-icon>
            </span>
            <div class="service-info">
              <div><b>ID:</b> {{ service.col_id }}</div>
              <div><b>Название:</b> {{ service.col_title }}</div>
            </div>
            <div
              class="service-video"
              @click="openPreview(service.col_video)"
              role="button"
              tabindex="0"
              @keydown.enter.prevent="openPreview(service.col_video)"
            >
              <video
                v-if="service.col_video"
                autoplay
                loop
                muted
                playsinline
                style="max-width: 180px; max-height: 120px; cursor: pointer;"
                :key="service.col_video"
              >
                <source :src="videoUrl(service.col_video)" :type="getVideoMimeType(service.col_video)" />
                Ваш браузер не поддерживает видео.
              </video>
              <div v-else class="no-video">Нет видео</div>
            </div>
            <div class="service-actions">
              <a href="#" class="icon2" title="Редактировать" @click.prevent="editService(service)" tabindex="0"><el-icon><Edit /></el-icon></a>
              <a href="#" class="icon2" title="Удалить" @click.prevent="deleteService(service.col_id)" tabindex="0"><el-icon><Delete /></el-icon></a>
            </div>
          </div>
        </template>
      </draggable>
    </section>

    <p v-else>Нет данных</p>

    <!-- Модалка добавления/редактирования услуги -->
    <el-dialog
      v-model="addModalVisible"
      :title="isEditing ? 'Редактировать услугу' : 'Добавить новую услугу'"
      width="60%"
      :before-close="handleBeforeClose"
      destroy-on-close
      append-to-body
    >
      <el-form :model="newService" label-position="top" ref="addForm">
        <el-form-item label="Название услуги" required>
          <el-input v-model="newService.col_title" placeholder="Введите название услуги" />
        </el-form-item>

        <el-form-item label="Описание услуги" required>
            <TinyEditor v-model="newService.col_description" />
        </el-form-item>

        <el-form-item label="Видео">
          <div class="media-preview" @click="openFileManager">
            <div
              class="media-size"
              v-if="mediaPreviewRealSize.width && mediaPreviewRealSize.height"
              style="margin-bottom: 5px; font-weight: 600; color: #333;"
            >
              Размер: {{ mediaPreviewRealSize.width }} × {{ mediaPreviewRealSize.height }} px
            </div>

            <el-alert
              v-if="videoFormatError"
              title="Видео формата .webm отсутствует!"
              type="warning"
              show-icon
              :closable="false"
              style="margin-bottom: 8px;"
            />

            <template v-if="!videoFormatError && mediaPreview.type === 'video'">
              <video
                ref="mediaPreviewVideoRef"
                autoplay
                loop
                muted
                playsinline
                style="max-width: 100%; max-height: 180px; border-radius: 8px; cursor: pointer;"
                :key="mediaPreview.link"
                @loadedmetadata="onLoadedMetadataMediaPreview"
              >
                <source :src="mediaPreview.link" :type="getVideoMimeType(mediaPreview.link)" />
                Ваш браузер не поддерживает видео.
              </video>
            </template>

            <template v-if="!mediaPreview.link && !videoFormatError">
              <div class="no-media">Кликните для выбора видео формата .webm</div>
            </template>
          </div>
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="closeAddModal">Отмена</el-button>
        <el-button
          type="primary"
          :loading="adding"
          @click="isEditing ? updateService() : submitNewService()"
          :disabled="videoFormatError"
        >
          {{ isEditing ? 'Сохранить' : 'Добавить' }}
        </el-button>
      </template>
    </el-dialog>

    <!-- Модалка предпросмотра видео -->
    <el-dialog
      :visible.sync="previewVisible"
      title="Предпросмотр видео"
      width="60%"
      :before-close="closePreview"
      append-to-body
      center
    >
      <div v-if="previewVisible && previewVideo" class="preview-content">
        <div class="video-size" v-if="previewRealSize.width && previewRealSize.height" style="margin-bottom: 5px;">
          Реальный размер: {{ previewRealSize.width }} × {{ previewRealSize.height }} px
        </div>

        <video
          ref="previewVideoRef"
          controls
          autoplay
          loop
          muted
          playsinline
          :style="{
            width: previewDisplaySize.width + 'px',
            height: previewDisplaySize.height + 'px',
            borderRadius: '8px',
            display: 'block',
            margin: '10px auto 0'
          }"
          @loadedmetadata="onLoadedMetadata"
        >
          <source :src="previewVideo" :type="getVideoMimeType(previewVideo)" />
          Ваш браузер не поддерживает видео.
        </video>
      </div>
      <p v-else>Видео отсутствует</p>
    </el-dialog>

    <!-- Файловый менеджер -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal" @click.self="closeFileManager">
        <div class="finder-homegrid">
          <vue-finder
            id="vuefinder"
            :request="vueFinderRequest"
            @select="handleFileSelect"
          />
          <button class="close-btn" @click="closeFileManager">✖</button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import TinyEditor from '@/components/admin/TinyEditor.vue'
import SeoServices from '@/components/admin/SeoServices.vue'
import { ref, nextTick, onMounted } from 'vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import { ElNotification, ElMessageBox } from 'element-plus'
import { Menu, Check, Rank, Plus, EditPen, Delete } from '@element-plus/icons-vue'

const seoForm = ref({
  seo_title: '',
  seo_description: '',
  seo_keywords: []
})

const loadSeo = async () => {
  try {
    const res = await axios.get('/api/admin/pages/services-seo')
    seoForm.value.seo_title = res.data.seo_title || ''
    seoForm.value.seo_description = res.data.seo_description || ''
    seoForm.value.seo_keywords = res.data.seo_keywords
      ? res.data.seo_keywords.split(',').map(s => s.trim())
      : []
  } catch {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при загрузке SEO',
      type: 'error'
    })
  }
}

const services = ref([])
const saving = ref(false)

const previewVisible = ref(false)
const previewVideo = ref('')
const previewVideoRef = ref(null)

const previewRealSize = ref({ width: 0, height: 0 })
const previewDisplaySize = ref({ width: 0, height: 0 })

const previewVideoExtension = ref('')

const mediaPreviewVideoSize = ref({ width: 0, height: 0 })
const mediaPreviewRealSize = ref({ width: 0, height: 0 })

const addModalVisible = ref(false)
const adding = ref(false)
const videoFormatError = ref(false)

const isEditing = ref(false) // режим редактирования
const editingServiceId = ref(null) // id редактируемой услуги

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

const vueFinderRequest = {
  baseUrl: '/api/vuefinder',
  adapter: 'local',
  xsrfHeaderName: 'X-XSRF-TOKEN',
}

// Открытие модалки для добавления новой услуги
function openAddModal() {
  isEditing.value = false
  editingServiceId.value = null
  resetNewService()
  addModalVisible.value = true
  videoFormatError.value = false
}

// Сброс данных формы услуги
function resetNewService() {
  newService.value = {
    col_title: '',
    col_description: '',
    col_video: ''
  }
  mediaPreview.value = { type: '', link: '' }
  mediaPreviewRealSize.value = { width: 0, height: 0 }
  mediaPreviewVideoSize.value = { width: 0, height: 0 }
}

// Закрытие модалки
function closeAddModal() {
  addModalVisible.value = false
  videoFormatError.value = false
  isEditing.value = false
  editingServiceId.value = null
}

// Открытие модалки для редактирования услуги
function editService(service) {
  isEditing.value = true
  editingServiceId.value = service.col_id

  newService.value.col_title = service.col_title
  newService.value.col_description = service.col_description

  if (service.col_video) {
    const videoPath = service.col_video.startsWith('/multimedia/')
      ? service.col_video
      : `/multimedia/${service.col_video}`
    mediaPreview.value = { type: 'video', link: videoPath }
  } else {
    mediaPreview.value = { type: '', link: '' }
  }

  mediaPreviewRealSize.value = { width: 0, height: 0 }
  mediaPreviewVideoSize.value = { width: 0, height: 0 }
  videoFormatError.value = false

  addModalVisible.value = true
}

// Обновление услуги
async function updateService() {
  if (!newService.value.col_title.trim()) {
    ElNotification({ title: 'Ошибка', message: 'Название услуги обязательно', type: 'error' })
    return
  }
  if (videoFormatError.value) {
    ElNotification({ title: 'Ошибка', message: 'Пожалуйста, выберите видео формата .webm', type: 'error' })
    return
  }

  adding.value = true
  try {
    const payload = {
      col_title: newService.value.col_title,
      col_description: newService.value.col_description,
      col_video: mediaPreview.value.type === 'video' ? mediaPreview.value.link.replace(/^\/?multimedia\//, '') : '',
    }
    await axios.put(`/api/admin/tbl-services/${editingServiceId.value}`, payload)

    ElNotification({ title: 'Успешно', message: 'Услуга обновлена', type: 'success' })
    closeAddModal()
    await fetchServices()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors
      let errorMessages = ''
      for (const key in errors) {
        if (errors.hasOwnProperty(key)) {
          errorMessages += errors[key].join(' ') + ' '
        }
      }
      ElNotification({ title: 'Ошибка валидации', message: errorMessages, type: 'error' })
    } else {
      ElNotification({ title: 'Ошибка', message: 'Не удалось обновить услугу', type: 'error' })
    }
  } finally {
    adding.value = false
  }
}

// Добавление новой услуги
async function submitNewService() {
  if (!newService.value.col_title.trim()) {
    ElNotification({ title: 'Ошибка', message: 'Название услуги обязательно', type: 'error' })
    return
  }
  if (videoFormatError.value) {
    ElNotification({ title: 'Ошибка', message: 'Пожалуйста, выберите видео формата .webm', type: 'error' })
    return
  }

  adding.value = true
  try {
    const payload = {
      col_title: newService.value.col_title,
      col_description: newService.value.col_description,
      col_video: mediaPreview.value.type === 'video' ? mediaPreview.value.link.replace(/^\/?multimedia\//, '') : '',
    }
    await axios.post('/api/admin/tbl-services', payload)

    ElNotification({ title: 'Успешно', message: 'Услуга добавлена', type: 'success' })
    closeAddModal()
    await fetchServices()
  } catch (error) {
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors
      let errorMessages = ''
      for (const key in errors) {
        if (errors.hasOwnProperty(key)) {
          errorMessages += errors[key].join(' ') + ' '
        }
      }
      ElNotification({ title: 'Ошибка валидации', message: errorMessages, type: 'error' })
    } else {
      ElNotification({ title: 'Ошибка', message: 'Не удалось добавить услугу', type: 'error' })
    }
  } finally {
    adding.value = false
  }
}

// Удаление услуги
async function deleteService(col_id) {
  try {
    await ElMessageBox.confirm('Вы уверены, что хотите удалить эту услугу?', 'Подтверждение', {
      confirmButtonText: 'Удалить',
      cancelButtonText: 'Отмена',
      type: 'warning',
    })

    await axios.delete(`/api/admin/tbl-services/${col_id}`)

    ElNotification({ title: 'Успешно', message: 'Услуга удалена', type: 'success' })
    await fetchServices()
  } catch (error) {
    if (error !== 'cancel') {
      ElNotification({ title: 'Ошибка', message: 'Не удалось удалить услугу', type: 'error' })
    }
  }
}

function closeFileManager() {
  showFileManager.value = false
}

function openFileManager() {
  showFileManager.value = true
}

// Загрузка списка услуг с сервера
async function fetchServices() {
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

// Сохранение порядка услуг
async function saveServices() {
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

function getVideoExtension(url) {
  if (!url) return ''
  const cleanUrl = url.split('?')[0]
  return cleanUrl.split('.').pop().toLowerCase()
}

function getVideoMimeType(url) {
  if (!url) return 'video/mp4'
  const ext = getVideoExtension(url)
  switch (ext) {
    case 'mp4': return 'video/mp4'
    case 'webm': return 'video/webm'
    case 'ogg':
    case 'ogv': return 'video/ogg'
    default: return 'video/mp4'
  }
}

function openPreview(video) {
  const url = videoUrl(video)
  if (!url) return
  previewVideo.value = url
  previewVideoExtension.value = getVideoExtension(url) || ''
  previewVisible.value = true
  previewRealSize.value = { width: 0, height: 0 }
  previewDisplaySize.value = { width: 0, height: 0 }

  nextTick(() => {
    if (previewVideoRef.value) {
      previewVideoRef.value.load()
      previewVideoRef.value.play().catch(() => {})
    }
  })
}

function videoUrl(video) {
  if (!video) return ''
  if (video.startsWith('/multimedia/')) return video
  return `/multimedia/${video}`
}

function onLoadedMetadata(event) {
  const video = event.target
  const realW = video.videoWidth
  const realH = video.videoHeight
  previewRealSize.value = { width: realW, height: realH }

  const maxWidth = window.innerWidth * 0.6
  const maxHeight = window.innerHeight * 0.6
  const ratio = Math.min(maxWidth / realW, maxHeight / realH, 1)
  const displayW = Math.round(realW * ratio)
  const displayH = Math.round(realH * ratio)

  previewDisplaySize.value = { width: displayW, height: displayH }
}

function onLoadedMetadataMediaPreview(event) {
  const el = event.target
  let realW, realH

  if (el.tagName === 'VIDEO') {
    realW = el.videoWidth
    realH = el.videoHeight
  } else if (el.tagName === 'IMG') {
    realW = el.naturalWidth
    realH = el.naturalHeight
  }

  mediaPreviewRealSize.value = { width: realW, height: realH }

  const maxWidth = 320
  const maxHeight = 180
  const ratio = Math.min(maxWidth / realW, maxHeight / realH, 1)
  const displayW = Math.round(realW * ratio)
  const displayH = Math.round(realH * ratio)

  mediaPreviewVideoSize.value = { width: displayW, height: displayH }
}

function closePreview() {
  previewVisible.value = false
  previewVideo.value = ''
  previewRealSize.value = { width: 0, height: 0 }
  previewDisplaySize.value = { width: 0, height: 0 }
  previewVideoExtension.value = ''
}

// Обработка выбора файла из файлового менеджера с проверкой формата .webm
function handleFileSelect(items) {
  if (!items.length) return
  const file = items[0]

  const ext = file.path ? file.path.split('.').pop().toLowerCase() : ''
  let type = ''
  if (file.mime && file.mime.startsWith('video/')) type = 'video'
  else if (file.mime && file.mime.startsWith('image/')) type = 'img'
  else if (['mp4', 'webm', 'ogg', 'ogv'].includes(ext)) type = 'video'
  else if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) type = 'img'
  else type = ''

  let path = file.path.replace(/^local:\/\//, '')
  if (!path.startsWith('/multimedia/')) {
    path = `/multimedia/${path}`
  }

  if (type !== 'video' || ext !== 'webm') {
    videoFormatError.value = true
    mediaPreview.value = { type: '', link: '' }
    mediaPreviewRealSize.value = { width: 0, height: 0 }
    mediaPreviewVideoSize.value = { width: 0, height: 0 }
    ElNotification({
      title: 'Предупреждение',
      message: 'Видео формата .webm отсутствует! Пожалуйста, выберите видео с расширением .webm',
      type: 'warning',
      duration: 5000,
      position: 'top-right',
    })
    showFileManager.value = false
    return
  } else {
    videoFormatError.value = false
  }

  mediaPreview.value = { type, link: path }
  mediaPreviewRealSize.value = { width: 0, height: 0 }
  mediaPreviewVideoSize.value = { width: 0, height: 0 }
  showFileManager.value = false
}

onMounted(() => {
    loadSeo()
    fetchServices()

    })
</script>

<style scoped>
.page-wrap {
  padding: 24px 30px;
  max-width: 960px;
  margin: 0 auto;
  font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
  color: #2c3e50;
}

.header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 25px;
  user-select: none;
}

h1 {
  font-weight: 700;
  font-size: 1.9rem;
  color: #34495e;
  margin: 0;
  user-select: text;
}

.btn-add {
  font-weight: 600;
  font-size: 1.1rem;
  padding: 7px 16px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-save {
  margin-left: auto;
  font-weight: 600;
  font-size: 1.1rem;
  padding: 7px 18px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.services-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* Карточка услуги */
.service-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 16px 18px;
  background: #fff;
  box-shadow: 0 3px 8px rgb(0 0 0 / 0.12);
  border-radius: 12px;
  user-select: none;
  transition: box-shadow 0.25s ease, transform 0.15s ease;
  cursor: grab;
}

.service-item:active {
  cursor: grabbing;
  transform: scale(0.99);
  box-shadow: 0 6px 16px rgb(0 0 0 / 0.18);
}

.drag-handle {
  cursor: grab;
  color: #7f8c8d;
  font-size: 22px;
  user-select: none;
  display: flex;
  align-items: center;
  padding: 4px;
  transition: color 0.25s ease;
}

.drag-handle:hover {
  color: #409eff;
}

.service-info {
  flex-grow: 1;
  min-width: 180px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  color: #34495e;
}

.service-id {
  font-weight: 600;
  color: #7f8c8d;
  font-size: 0.9rem;
}

.service-id span {
  font-weight: 400;
  color: #34495e;
}

.service-title {
  font-weight: 700;
  font-size: 1.18rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.service-video {
  width: 180px;
  height: 120px;
  position: relative;
  cursor: pointer;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 1px 5px rgb(0 0 0 / 0.12);
  background: #f8f9fa;
  flex-shrink: 0;
  transition: box-shadow 0.3s ease;
}

.service-video:hover {
  box-shadow: 0 5px 15px rgb(0 0 0 / 0.25);
}

.video-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
  pointer-events: none;
  user-select: none;
}

.no-video {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #bdc3c7;
  font-style: italic;
  font-size: 0.95rem;
  user-select: none;
}

.service-actions {
  display: flex;
  gap: 14px;
  font-size: 24px;
  user-select: none;
  flex-shrink: 0;
}

.icon-btn {
  cursor: pointer;
  color: #7f8c8d;
  transition: color 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 8px;
}

.icon-btn:hover {
  color: #409eff;
  background: rgba(64, 158, 255, 0.1);
}

/* Модальное окно формы */
.form-service {
  font-size: 1rem;
}

.media-preview {
  border-radius: 10px;
  height: 180px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  background: #f0f8ff;
  margin-top: 5px;
  box-shadow: inset 0 0 6px rgb(0 123 255 / 0.3);
  transition: background 0.25s ease;
}

.media-preview:hover {
  background: #dbeeff;
}

.media-size {
  margin-bottom: 5px;
  font-weight: 600;
  color: #333;
  user-select: text;
}

.media-video {
  max-width: 100%;
  max-height: 180px;
  border-radius: 8px;
  cursor: pointer;
  user-select: none;
}

.no-media {
  color: #aaa;
  font-style: italic;
  user-select: none;
  padding: 0 10px;
}

.alert-video-error {
  margin-bottom: 8px;
}

/* Файловый менеджер */
.finder-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.6);
  z-index: 10500 !important;
  display: flex;
  justify-content: center;
  align-items: center;
}

.finder-homegrid {
  width: 92%;
  height: 90%;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
  box-shadow: 0 6px 20px rgb(0 0 0 / 0.25);
}

.close-btn {
  position: absolute;
  top: 5px;
  right: 95px;
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 7px 12px;
  cursor: pointer;
  font-size: 1.2rem;
  font-weight: 600;
  transition: background 0.3s ease;
  z-index: 15;
}

.close-btn:hover {
  background: #c0392b;
}

/* Предпросмотр видео */
.preview-content {
  text-align: center;
}

.video-size {
  font-weight: 600;
  font-size: 1.2rem;
  margin-bottom: 6px;
  color: #333;
}

.no-data {
  text-align: center;
  font-style: italic;
  color: #999;
  margin-top: 50px;
  font-size: 1.1rem;
  user-select: none;
}
</style>
