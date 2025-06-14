<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import TinyEditor from '@/components/admin/TinyEditor.vue'
import SeoServices from '@/components/admin/SeoServices.vue'
import { ElNotification } from 'element-plus'

const model = ref({
  col_poster: '',
  col_poster_alt: '',
  col_video: '',
  col_description: ''
})

const posterDimensions = ref('')
const videoDimensions = ref('')

const expanded = ref(null)
const showFileManager = ref(false)
const currentType = ref(null)

const posterSrc = computed(() => model.value.col_poster ? `/multimedia/${model.value.col_poster}` : '')
const videoSrc = computed(() => model.value.col_video ? `/multimedia/${model.value.col_video}` : '')

const openManager = (type) => {
  currentType.value = type
  showFileManager.value = true
}

const handleSelect = (items) => {
  if (!items.length) return
  const path = items[0].path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  model.value[currentType.value] = path
  showFileManager.value = false

  nextTick(() => {
    if (currentType.value === 'col_poster') updatePosterDimensions()
    if (currentType.value === 'col_video') updateVideoDimensions()
  })
}

const loadWorkflow = async () => {
  try {
    const { data } = await axios.get('/api/admin/workflow')
    model.value = {
      col_poster: data?.col_poster || '',
      col_poster_alt: data?.col_poster_alt || '',
      col_video: data?.col_video || '',
      col_description: data?.col_description || ''
    }
    await nextTick()
    updatePosterDimensions()
    updateVideoDimensions()
  } catch (e) {
    console.error('Ошибка загрузки workflow', e)
  }
}

const saveWorkflow = async () => {
  try {
    await axios.post('/api/admin/workflow', model.value)
    ElNotification({ title: 'Успешно', message: 'Сохранено', type: 'success' })
  } catch (e) {
    ElNotification({ title: 'Ошибка', message: 'Не удалось сохранить', type: 'error' })
  }
}

const updatePosterDimensions = () => {
  const img = new Image()
  img.onload = () => {
    posterDimensions.value = `${img.naturalWidth}x${img.naturalHeight}px`
  }
  img.src = posterSrc.value
}

const updateVideoDimensions = () => {
  const video = document.querySelector('.js-video')
  if (video) {
    video.onloadedmetadata = () => {
      videoDimensions.value = `${video.videoWidth}x${video.videoHeight}px`
    }
  }
}

const toggleExpand = (type) => {
  expanded.value = expanded.value === type ? null : type
}

onMounted(() => {
  axios.get('/sanctum/csrf-cookie')
  loadWorkflow()
})
</script>

<template>
  <div class="page-layout">
    <!-- Левая колонка: SEO -->
    <div class="left-column">
      <SeoServices title="SEO для Workflow" pageName="workflow" />
    </div>

    <!-- Правая колонка: Workflow контент -->
    <div class="right-column">
      <h3>🎬 Видео Workflow</h3>

      <!-- Постер -->
      <div class="media-box" :class="{ expanded: expanded === 'poster' }" @click="toggleExpand('poster')">
        <div class="media-label">Постер ({{ posterDimensions }})</div>
        <img :src="posterSrc" :alt="model.col_poster_alt || 'Постер'" />
        <div class="overlay">
          <span class="upload-icon" @click.stop="openManager('col_poster')">📤 Заменить</span>
        </div>
      </div>
      <el-input
        v-model="model.col_poster_alt"
        placeholder="Альтернативный текст для постера (alt)"
        style="margin-top: 10px"
      />
      <p class="filename">{{ model.col_poster }}</p>

      <!-- Видео -->
      <div class="media-box" :class="{ expanded: expanded === 'video' }" @click="toggleExpand('video')">
        <div class="media-label">Видео ({{ videoDimensions }})</div>
        <video class="js-video" :key="videoSrc" autoplay loop muted playsinline>
          <source :src="videoSrc" :type="'video/' + videoSrc.split('.').pop()" />
          Ваш браузер не поддерживает видео.
        </video>
        <div class="overlay">
          <span class="upload-icon" @click.stop="openManager('col_video')">📤 Заменить</span>
        </div>
      </div>
      <p class="filename">{{ model.col_video }}</p>

      <!-- TinyMCE -->
      <div class="editor-wrap">
        <h4>Описание</h4>
        <TinyEditor v-model="model.col_description" />
      </div>

      <!-- Кнопки -->
      <div class="save-button">
        <el-button type="success" @click="saveWorkflow">Сохранить</el-button>
      </div>
    </div>

    <!-- VueFinder -->
    <teleport to="body">
      <div v-if="showFileManager" class="finder-modal" @click.self="showFileManager = false">
        <div class="finder-container">
          <vue-finder
            :request="{ baseUrl: '/api/vuefinder', adapter: 'local', xsrfHeaderName: 'X-XSRF-TOKEN' }"
            @select="handleSelect"
          />
          <button class="close-btn" @click="showFileManager = false">✖</button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<style scoped>
.page-layout {
  display: flex;
  gap: 20px;
  align-items: flex-start;
}
.left-column {
  width: 50%;
}
.right-column {
  width: 50%;
  background: white;
  padding: 20px;
  border-radius: 8px;
}
.media-box {
  position: relative;
  margin-top: 20px;
  cursor: pointer;
  border: 2px dashed #ccc;
  border-radius: 6px;
  overflow: hidden;
  width: 100%;
  height: 180px;
  transition: all 0.4s ease;
}
.media-box.expanded {
  height: 405px;
}
.media-box img,
.media-box video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}
.media-label {
  position: absolute;
  top: 5px;
  left: 8px;
  background: rgba(0,0,0,0.6);
  color: #fff;
  padding: 2px 8px;
  font-size: 13px;
  z-index: 2;
  border-radius: 4px;
}
.overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: flex-end;
  justify-content: center;
  padding: 10px;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.media-box:hover .overlay {
  opacity: 1;
}
.upload-icon {
  font-size: 14px;
  background: rgba(0, 0, 0, 0.7);
  padding: 6px 12px;
  border-radius: 6px;
  color: #fff;
  cursor: pointer;
}
.filename {
  font-size: 13px;
  color: #555;
  margin: 5px 0 15px;
  word-break: break-word;
}
.editor-wrap {
  margin-top: 30px;
}
.save-button {
  margin-top: 20px;
  text-align: right;
}
.finder-modal {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0, 0, 0, 0.5);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
}
.finder-container {
  width: 90%;
  height: 90%;
  background: #fff;
  border-radius: 8px;
  overflow: hidden;
  position: relative;
}
.close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  background: #e00;
  color: #fff;
  padding: 5px 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  z-index: 10;
}
</style>
