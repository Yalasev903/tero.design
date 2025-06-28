<template>
  <div class="showreel-block">
    <h3>🎬 Видео в модальном окне</h3>

    <!-- Постер -->
    <div class="media-box" :class="{ expanded: expanded === 'poster' }" @click="toggleExpand('poster')">
      <img :src="posterSrc" alt="Постер" />
      <div class="overlay">
        <span class="size">1920x1080</span>
        <span class="upload-icon" @click.stop="openManager('poster')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ model.poster }}</p>

    <!-- Видео -->
    <div class="media-box" :class="{ expanded: expanded === 'video' }" @click="toggleExpand('video')">
      <video :key="videoSrc" autoplay loop muted playsinline>
        <source :src="videoSrc" type="video/mp4" />
        <source :src="videoWebmSrc" type="video/webm" />
        Ваш браузер не поддерживает видео.
      </video>
      <div class="overlay">
        <span class="size">1920x1080</span>
        <span class="upload-icon" @click.stop="openManager('video')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ model.video }}</p>

    <!-- Кнопка сохранения -->
    <div class="save-button">
      <el-button type="success" @click="saveMedia">Сохранить</el-button>
    </div>

    <!-- VueFinder -->
    <teleport to="body">
      <div v-if="showModal" class="finder-modal">
        <div class="finder-container">
          <vue-finder
            id="vuefinder"
            :request="{
              baseUrl: '/api/vuefinder',
              adapter: 'local',
              xsrfHeaderName: 'X-XSRF-TOKEN'
            }"
            @select="handleSelect"
          />
          <button class="close-btn" @click="showModal = false">✖</button>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
defineOptions({
  name: 'ShowreelPreview'
})
import { ref, reactive, computed, onMounted, onActivated  } from 'vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'
import {
  Plus,
  Check,
  Delete,
  Edit,
  Picture,
  Menu,
  Iphone,
  VideoCamera
} from '@element-plus/icons-vue'


const model = reactive({ poster: '', video: '' })
const currentType = ref('poster')
const expanded = ref(null)
const showModal = ref(false)

const openManager = (type) => {
  currentType.value = type
  showModal.value = true
}

const handleSelect = (items) => {
  if (!items.length) return

  const path = items[0].path
    .replace(/^local:\/\//, '')
    .replace(/^multimedia\//, '')

  model[currentType.value] = path
  showModal.value = false
}

const loadShowreel = async () => {
  const { data } = await axios.get('/api/admin/showreel')
  if (!data?.media) return

  if (data.media?.type === 'video') {
    model.poster = data.media?.poster || ''
    model.video = data.media?.links?.[0]?.link || ''
  } else if (data.media?.type === 'img') {
    model.poster = data.media?.link || ''
  }
}

const saveMedia = async () => {
  try {
    const media = {
      type: 'video',
      poster: model.poster || '',
      links: [],
      width: 1920,
      height: 1080
    }

    if (model.video) {
      media.links.push({ link: model.video, mime: 'video/mp4' })
    }

    await axios.post('/api/admin/showreel', { media })

    ElNotification({
      title: 'Успешно',
      message: 'Showreel обновлён',
      type: 'success',
      duration: 3000
    })
  } catch (error) {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось сохранить Showreel',
      type: 'error',
      duration: 3000
    })
  }
}

const posterSrc = computed(() =>
  model.poster ? `/multimedia/${model.poster}` : '/multimedia/showreel_2023/obl-2023_2.jpg'
)
const videoSrc = computed(() =>
  model.video ? `/multimedia/${model.video}` : '/multimedia/showreel/Showreel_2024_HD.mp4'
)
const videoWebmSrc = computed(() => {
  if (!model.video) return '/multimedia/showreel/Showreel_2024_HD.webm'

  const webm = model.video.replace(/\.(mp4|mov|mkv)$/i, '.webm')
  return `/multimedia/${webm}`
})
const toggleExpand = (type) => {
  expanded.value = expanded.value === type ? null : type
}

onMounted(() => {
  axios.get('/sanctum/csrf-cookie')
  loadShowreel()
})
onActivated(loadShowreel)
</script>

<style scoped>
.showreel-block {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 3px rgba(0, 0, 0, 0.05);
}
.media-box {
  position: relative;
  margin-top: 20px;
  cursor: pointer;
  border: 2px dashed #ccc;
  border-radius: 6px;
  overflow: hidden;
  width: 320px;
  height: 180px;
  transition: all 0.4s ease;
}
.media-box.expanded {
  width: 100%;
  max-width: 720px;
  height: 405px;
  z-index: 2;
}
.media-box img,
.media-box video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 6px;
}
.overlay {
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(30, 30, 30, 0.45);
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: space-between;
  padding: 8px;
  color: #fff;
  opacity: 0;
  transition: opacity 0.3s ease;
}
.media-box:hover .overlay { opacity: 1; }
.size {
  font-size: 12px;
  background: rgba(0, 0, 0, 0.5);
  padding: 2px 6px;
  border-radius: 4px;
  align-self: flex-end;
}
.upload-icon {
  font-size: 13px;
  background: rgba(0, 0, 0, 0.7);
  padding: 4px 10px;
  border-radius: 6px;
  align-self: center;
  cursor: pointer;
}
.filename {
  font-size: 13px;
  color: #555;
  margin: 5px 0 15px;
  word-break: break-word;
}
.save-button {
  margin-top: 15px;
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
  top: 5px;
  right: 95px;
  background: #e00;
  color: #fff;
  padding: 5px 10px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  z-index: 10;
}
</style>
