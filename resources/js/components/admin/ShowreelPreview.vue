<template>
  <div class="showreel-block">
    <h3>🎬 Видео в модальном окне</h3>

    <!-- Постер -->
    <div
      class="media-box"
      :class="{ expanded: expanded === 'poster' }"
      @click="toggleExpand('poster')"
    >
      <img :src="posterSrc" alt="Постер" />
      <div class="overlay">
        <span class="size">1920x1080</span>
        <span class="upload-icon" @click.stop="openFileManager('poster')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ model.poster }}</p>

    <!-- Видео -->
    <div
      class="media-box"
      :class="{ expanded: expanded === 'video' }"
      @click="toggleExpand('video')"
    >
      <video autoplay loop muted playsinline>
        <source :src="videoSrc" type="video/mp4" />
        <source :src="videoWebmSrc" type="video/webm" />
        Ваш браузер не поддерживает видео.
      </video>
      <div class="overlay">
        <span class="size">1920x1080</span>
        <span class="upload-icon" @click.stop="openFileManager('video')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ model.video }}</p>

    <!-- Кнопка сохранения -->
    <div class="save-button">
      <el-button type="success" @click="saveMedia">Сохранить</el-button>
    </div>

    <!-- Модальное окно с FileManager -->
    <el-dialog v-model="isModalOpen" title="Выберите файл" width="80%" top="5vh">
      <iframe
        :src="filemanagerUrl"
        style="width:100%; height:70vh; border:0"
      />
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Локальное состояние вместо props.modelValue
const model = reactive({
  poster: '',
  video: ''
})

const fileInput = ref(null)
const currentType = ref('poster')
const expanded = ref(null)
const isModalOpen = ref(false)
const filemanagerUrl = ref('/filemanager?type=file')

const toggleExpand = (type) => {
  expanded.value = expanded.value === type ? null : type
}

const openFileManager = (type) => {
  currentType.value = type
  isModalOpen.value = true
}

const handleFileSelected = (e) => {
  if (typeof e.data !== 'string') return
  if (!e.data.startsWith('/multimedia/')) return
  const filename = e.data.replace('/multimedia/', '')
  model[currentType.value] = filename
  isModalOpen.value = false
}

onMounted(() => window.addEventListener('message', handleFileSelected))
onUnmounted(() => window.removeEventListener('message', handleFileSelected))

// Сохранение через новый контроллер
const saveMedia = async () => {
  if (model.poster) {
    await axios.post('/api/admin/showreel', {
      type: 'img',
      file: model.poster,
      width: 1920,
      height: 1080
    })
  }

  if (model.video) {
    await axios.post('/api/admin/showreel', {
      type: 'video',
      file: model.video,
      mime: 'video/mp4',
      width: 1920,
      height: 1080
    })
  }

  alert('Медиафайлы успешно сохранены.')
}

// Подгрузка текущих данных
const loadShowreel = async () => {
  const { data } = await axios.get('/api/admin/showreel')
  if (data?.media?.type === 'img') model.poster = data.media.link
  if (data?.media?.type === 'video') model.video = data.media.links?.[0]?.link
}
onMounted(loadShowreel)

const posterSrc = computed(() =>
  model.poster ? `/multimedia/${model.poster}` : '/multimedia/showreel_2023/obl-2023_2.jpg'
)

const videoSrc = computed(() =>
  model.video ? `/multimedia/${model.video}` : '/multimedia/showreel/Showreel_2024_HD.mp4'
)

const videoWebmSrc = computed(() =>
  model.video ? `/multimedia/${model.video}` : '/multimedia/showreel/Showreel_2024_HD.webm'
)
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
</style>
