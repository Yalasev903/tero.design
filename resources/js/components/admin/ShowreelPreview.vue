<template>
  <div class="showreel-block">
    <h3>🎬 Вставка Vimeo-ссылки</h3>

    <el-input
      v-model="vimeoLink"
      placeholder="https://player.vimeo.com/video/123456789"
      clearable
    ></el-input>

    <div class="vimeo-preview" v-if="vimeoLink">
      <div style="padding:56.25% 0 0 0;position:relative;">
        <iframe :src="vimeoLink"
                frameborder="0"
                allow="autoplay; fullscreen; picture-in-picture"
                style="position:absolute;top:0;left:0;width:100%;height:100%;"
                title="Vimeo Preview"
        ></iframe>
      </div>
    </div>

    <div class="save-button">
      <el-button type="success" @click="saveMedia">Сохранить</el-button>
    </div>
  </div>
</template>

<script setup>
defineOptions({
  name: 'ShowreelPreview'
})
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'

const vimeoLink = ref('')

const loadShowreel = async () => {
  const { data } = await axios.get('/api/admin/showreel')
  if (data?.media?.type === 'vimeo') {
    vimeoLink.value = data.media.link || ''
  }
}

const saveMedia = async () => {
  try {
    await axios.post('/api/admin/showreel', { vimeo_link: vimeoLink.value })

    ElNotification({
      title: 'Успешно',
      message: 'Ссылка Vimeo сохранена',
      type: 'success',
      duration: 3000
    })
  } catch {
    ElNotification({
      title: 'Ошибка',
      message: 'Не удалось сохранить ссылку',
      type: 'error',
      duration: 3000
    })
  }
}

onMounted(() => {
  axios.get('/sanctum/csrf-cookie')
  loadShowreel()
})
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
.vimeo-preview {
  margin-top: 20px;
  width: 100%;
  max-width: 720px;
}
</style>
