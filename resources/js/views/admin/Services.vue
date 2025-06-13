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
                :key="service.col_video"
              >
                <source :src="videoUrl(service.col_video)" :type="getVideoMimeType(service.col_video)" />
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

    <!-- Модалка добавления услуги -->
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

        <el-form-item label="Описание услуги" required>
          <el-input
            type="textarea"
            v-model="newService.col_description"
            placeholder="Введите описание услуги"
            :rows="6"
          />
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
        <el-button type="primary" :loading="adding" @click="submitNewService" :disabled="videoFormatError">Добавить</el-button>
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
import { ref, nextTick, onMounted } from 'vue'
import draggable from 'vuedraggable'
import axios from 'axios'
import { ElNotification, ElMessageBox } from 'element-plus'
import { Menu, Check } from '@element-plus/icons-vue'

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

// Открытие модалки добавления
function openAddModal() {
  addModalVisible.value = true
  newService.value = { col_title: '', col_description: '', col_video: '' }
  mediaPreview.value = { type: '', link: '' }
  mediaPreviewRealSize.value = { width: 0, height: 0 }
  mediaPreviewVideoSize.value = { width: 0, height: 0 }
  videoFormatError.value = false
}

// Закрытие модалки добавления
function closeAddModal() {
  addModalVisible.value = false
  videoFormatError.value = false
}

// Закрытие файлового менеджера
function closeFileManager() {
  showFileManager.value = false
}

// Открытие файлового менеджера
function openFileManager() {
  showFileManager.value = true
}

// Получение списка услуг
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

// Удаление услуги
async function deleteService(id) {
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

// Формируем URL для видео
const videoUrl = (video) => {
  if (!video) return ''
  if (video.startsWith('/multimedia/')) return video
  return `/multimedia/${video}`
}

// Получаем расширение файла из URL
function getVideoExtension(url) {
  if (!url) return ''
  const cleanUrl = url.split('?')[0]
  return cleanUrl.split('.').pop().toLowerCase()
}

// Получаем MIME тип для видео по расширению
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

// Открываем модалку предпросмотра видео
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

// Обработка загрузки метаданных видео для предпросмотра
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

// Обработка загрузки метаданных видео для превью в форме
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

  // Масштабируем под max 320x180 для превью
  const maxWidth = 320
  const maxHeight = 180
  const ratio = Math.min(maxWidth / realW, maxHeight / realH, 1)
  const displayW = Math.round(realW * ratio)
  const displayH = Math.round(realH * ratio)

  mediaPreviewVideoSize.value = { width: displayW, height: displayH }
}

// Закрываем модалку предпросмотра
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

  // Если файл не видео или не .webm - показываем ошибку и закрываем файловый менеджер
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
    // Закрываем файловый менеджер, чтобы уведомление было видно
    showFileManager.value = false
    return
  } else {
    videoFormatError.value = false
  }

  // Корректный файл - принимаем и закрываем файловый менеджер
  mediaPreview.value = { type, link: path }
  mediaPreviewRealSize.value = { width: 0, height: 0 }
  mediaPreviewVideoSize.value = { width: 0, height: 0 }
  showFileManager.value = false
}

// Отправка новой услуги
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
  border-radius: 8px;
  height: 180px;
  display: flex;
  flex-direction: column; /* чтобы размер был над медиаконтентом */
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
  z-index: 10500 !important; /* Увеличенный z-index */
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
.preview-content {
  text-align: center;
}
.video-size {
  font-weight: 600;
  font-size: 1.2rem;
  margin-bottom: 6px;
  color: #333;
}
</style>
