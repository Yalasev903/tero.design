<template>
  <div class="panel-wrapper">
    <h2>Панель управления</h2>

    <div class="panel-section">
      <el-switch v-model="form.jivochat" active-text="JivoChat включен" inactive-text="Отключен" />
    </div>

    <el-row :gutter="20">
      <el-col :span="12">
        <el-card>
          <h3>Соцсети и Контакты</h3>
          <el-input v-model="form.behance" placeholder="Behance" />
          <el-input v-model="form.facebook" placeholder="Facebook" />
          <el-input v-model="form.instagram" placeholder="Instagram" />
          <el-input v-model="form.linkedin" placeholder="LinkedIn" />
          <el-input v-model="form.pinterest" placeholder="Pinterest" />
          <el-input v-model="form.youtube" placeholder="YouTube" />

            <div class="save-button">
                <el-button type="success" @click="saveSettings">Сохранить</el-button>
            </div>
        </el-card>

        <el-card style="margin-top: 20px">
          <h3>SEO home.index</h3>
          <el-input v-model="form.seo_title" placeholder="Title" />
          <el-input
            type="textarea"
            v-model="form.seo_description"
            placeholder="Description"
            :rows="2"
          />
          <el-select
            v-model="form.seo_keywords"
            multiple
            allow-create
            filterable
            default-first-option
            placeholder="Введите ключевые слова и нажимайте Enter"
            style="width: 100%"
          >
          </el-select>

          <div class="save-button">
            <el-button type="success" @click="saveSeo">Сохранить</el-button>
          </div>
        </el-card>
      </el-col>

      <el-col :span="12">
        <el-card>
          <ShowreelPreview v-model="form" />
        </el-card>

        <el-card style="margin-top: 20px">
          <h3>Карта</h3>
          <el-input v-model="form.lat" placeholder="Lat" />
          <el-input v-model="form.lng" placeholder="Lng" />
          <el-input v-model="form.zoom" placeholder="Zoom" />
          <el-input v-model="form.google_key" placeholder="Google API Key" />

            <div class="save-button">
                <el-button type="success" @click="saveMap">Сохранить карту</el-button>
            </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'
import ShowreelPreview from '@/components/admin/ShowreelPreview.vue'

const form = ref({
  jivochat: false,
  jivochat_id: '',
  email: '',
  tel: '',
  facebook: '',
  instagram: '',
  linkedin: '',
  pinterest: '',
  youtube: '',
  behance: '',
  seo_title: '',
  seo_description: '',
  seo_keywords: [],
  poster: '',
  video: '',
  lat: '',
  lng: '',
  zoom: '',
  google_key: ''
})

const load = async () => {
  // SEO
  const seoRes = await axios.get('/api/admin/pages/home-seo')
  form.value.seo_title = seoRes.data.seo_title || ''
  form.value.seo_description = seoRes.data.seo_description || ''
  form.value.seo_keywords = seoRes.data.seo_keywords
    ? seoRes.data.seo_keywords.split(',').map(s => s.trim())
    : []

  // Settings (соцсети, контакты, карта и т.д.)
  const settingsRes = await axios.get('/api/admin/settings')
  const data = settingsRes.data
  form.value.jivochat = data.jivochat
  form.value.jivochat_id = data.jivochat_id
  form.value.email = data.email
  form.value.tel = data.tel
  form.value.behance = data.behance || ''
  form.value.facebook = data.facebook || ''
  form.value.instagram = data.instagram || ''
  form.value.linkedin = data.linkedin || ''
  form.value.pinterest = data.pinterest || ''
  form.value.youtube = data.youtube || ''
  form.value.poster = data.poster || ''
  form.value.video = data.video || ''
  form.value.lat = data.lat
  form.value.lng = data.lng
  form.value.zoom = data.zoom
  form.value.google_key = data.google_key
}

const saveSeo = async () => {
  try {
    await axios.post('/api/admin/pages/home-seo', {
      seo_title: form.value.seo_title,
      seo_description: form.value.seo_description,
      seo_keywords: form.value.seo_keywords.join(',')
    })

    ElNotification({
      title: 'Успешно',
      message: 'SEO данные обновлены',
      type: 'success'
    })
  } catch {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при сохранении SEO',
      type: 'error'
    })
  }
}

const saveSettings = async () => {
  try {
    await axios.post('/api/admin/settings', {
      jivochat: form.value.jivochat,
      jivochat_id: form.value.jivochat_id,
      email: form.value.email,
      tel: form.value.tel,
      behance: form.value.behance,
      facebook: form.value.facebook,
      instagram: form.value.instagram,
      linkedin: form.value.linkedin,
      pinterest: form.value.pinterest,
      youtube: form.value.youtube,
      poster: form.value.poster,
      video: form.value.video,
      lat: form.value.lat,
      lng: form.value.lng,
      zoom: form.value.zoom,
      google_key: form.value.google_key
    })

    ElNotification({
      title: 'Успешно',
      message: 'Настройки сохранены',
      type: 'success'
    })
  } catch {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при сохранении настроек',
      type: 'error'
    })
  }
}

const saveMap = async () => {
  try {
    await axios.post('/api/admin/settings/map', {
      lat: form.value.lat,
      lng: form.value.lng,
      zoom: form.value.zoom,
      google_key: form.value.google_key
    })

    ElNotification({
      title: 'Успешно',
      message: 'Карта сохранена',
      type: 'success'
    })
  } catch {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при сохранении карты',
      type: 'error'
    })
  }
}

onMounted(load)
</script>

<style scoped>
.panel-wrapper {
  padding: 20px;
}
.panel-section {
  margin-bottom: 20px;
}
.save-button {
  margin-top: 15px;
  text-align: right;
}
:deep(.el-select .el-tag) {
  background-color: transparent !important;
  border: 1px solid rgb(64, 255, 89) !important;
  color: #409EFF !important;
  font-weight: 500;
  border-radius: 6px;
}
</style>
