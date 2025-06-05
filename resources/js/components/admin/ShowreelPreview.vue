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
        <span class="upload-icon" @click.stop="select('poster')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ posterFilename }}</p>

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
        <span class="upload-icon" @click.stop="select('video')">📤 Заменить</span>
      </div>
    </div>
    <p class="filename">{{ videoFilename }}</p>

    <!-- Кнопка сохранения -->
    <div class="save-button">
      <el-button type="success" @click="saveMedia">Сохранить</el-button>
    </div>

    <!-- Инпут загрузки -->
    <input
      type="file"
      ref="fileInput"
      class="hidden"
      @change="uploadFile"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import axios from 'axios'

const props = defineProps(['modelValue'])
const emit = defineEmits(['update:modelValue'])

const fileInput = ref(null)
const currentType = ref('poster')
const expanded = ref(null)

const toggleExpand = (type) => {
  expanded.value = expanded.value === type ? null : type
}

const select = (type) => {
  currentType.value = type
  fileInput.value.click()
}

const uploadFile = async (e) => {
  const file = e.target.files[0]
  if (!file) return

  const fd = new FormData()
  fd.append('file', file)

  const { data } = await axios.post('/api/admin/upload', fd)
  emit('update:modelValue', {
    ...props.modelValue,
    [currentType.value]: data.filename
  })
}

// Сохранить на сервер
const saveMedia = async () => {
  await axios.post('/api/admin/settings', {
    poster: props.modelValue.poster,
    video: props.modelValue.video
  })
  alert('Медиафайлы сохранены')
}

// Дефолтные пути
const defaultPoster = '/multimedia/showreel_2023/obl-2023_2.jpg'
const defaultVideoMp4 = '/multimedia/showreel/Showreel_2024_HD.mp4'
const defaultVideoWebm = '/multimedia/showreel/Showreel_2024_HD.webm'

const posterSrc = computed(() =>
  props.modelValue.poster ? `/multimedia/${props.modelValue.poster}` : defaultPoster
)

const videoSrc = computed(() =>
  props.modelValue.video ? `/multimedia/${props.modelValue.video}` : defaultVideoMp4
)

const videoWebmSrc = computed(() =>
  props.modelValue.video ? `/multimedia/${props.modelValue.video}` : defaultVideoWebm
)

const posterFilename = computed(() =>
  props.modelValue.poster || 'obl-2023_2.jpg'
)

const videoFilename = computed(() =>
  props.modelValue.video || 'Showreel_2024_HD.mp4'
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
  transition: transform 0.4s ease, width 0.4s ease, height 0.4s ease;
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
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
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

.media-box:hover .overlay {
  opacity: 1;
}

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
  word-break: break-all;
}

.save-button {
  margin-top: 15px;
  text-align: right;
}

.hidden {
  display: none;
}
</style>
