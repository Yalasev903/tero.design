<template>
  <div class="panel-wrapper">
    <h2>Панель управления</h2>

    <el-row :gutter="20">
      <!-- Левая колонка -->
      <el-col :span="12">
        <!-- Соцсети -->
        <el-card class="mb20">
          <h2>Соцсети</h2>
          <el-form label-position="top">
            <el-form-item label="Ссылка на Behance">
              <el-input v-model="form.behance" placeholder="Ссылка на Behance" />
            </el-form-item>
            <el-form-item label="Ссылка на Facebook">
              <el-input v-model="form.facebook" placeholder="Ссылка на Facebook" />
            </el-form-item>
            <el-form-item label="Ссылка на Instagram">
              <el-input v-model="form.instagram" placeholder="Ссылка на Instagram" />
            </el-form-item>
            <el-form-item label="Ссылка на LinkedIn">
              <el-input v-model="form.linkedin" placeholder="Ссылка на LinkedIn" />
            </el-form-item>
            <el-form-item label="Ссылка на Pinterest">
              <el-input v-model="form.pinterest" placeholder="Ссылка на Pinterest" />
            </el-form-item>
            <el-form-item label="Ссылка на YouTube">
              <el-input v-model="form.youtube" placeholder="Ссылка на YouTube" />
            </el-form-item>
          </el-form>
        </el-card>

        <!-- JivoChat -->
        <el-card class="mb20">
          <h3>JivoChat</h3>
          <el-form label-position="top">
            <el-form-item>
              <el-switch
                v-model="form.jivochat"
                active-text="Включить JivoChat на всех страницах"
                inactive-text="Отключить"
              />
            </el-form-item>
            <el-form-item label="JivoChat ID">
              <el-input v-model="form.jivochat_id" placeholder="JivoChat ID" />
            </el-form-item>
            <el-form-item label="Email">
              <el-input v-model="form.email" placeholder="Email" />
            </el-form-item>
            <el-form-item label="Телефон">
              <el-input v-model="form.tel" placeholder="Телефон" />
            </el-form-item>
            <el-form-item label="Google Tag Manager">
              <el-input
                v-model="form.google_tm"
                type="textarea"
                :rows="3"
                placeholder="Google Tag Manager (gtag.js)"
              />
            </el-form-item>
            <div class="save-button">
              <el-button type="success" @click="saveSettings">Сохранить</el-button>
            </div>
          </el-form>
        </el-card>

        <!-- Showreel -->
        <el-card>
          <ShowreelPreview v-model="form" />
        </el-card>
      </el-col>

      <!-- Правая колонка -->
      <el-col :span="12">
        <!-- SEO -->
        <el-card class="mb20">
          <h3>SEO home.index</h3>
          <el-form label-position="top">
            <el-form-item label="Заголовок (title)">
              <el-input v-model="form.seo_title" placeholder="Title" />
            </el-form-item>
            <el-form-item label="Описание (description)">
              <el-input
                type="textarea"
                v-model="form.seo_description"
                placeholder="Description"
                :rows="2"
              />
            </el-form-item>
            <el-form-item label="Ключевые слова (keywords)">
              <el-select
                v-model="form.seo_keywords"
                multiple
                allow-create
                filterable
                default-first-option
                placeholder="Ключевые слова"
                style="width: 100%"
              />
            </el-form-item>
            <div class="save-button">
              <el-button type="success" @click="saveSeo">Сохранить</el-button>
            </div>
          </el-form>
        </el-card>

        <!-- Карта -->
        <el-card>
          <h3>Карта</h3>
          <el-form label-position="top" class="map-form">
            <el-form-item label="Координаты Lat">
              <el-input v-model="form.lat" placeholder="Широта" />
            </el-form-item>
            <el-form-item label="Координаты Lng">
              <el-input v-model="form.lng" placeholder="Долгота" />
            </el-form-item>
            <el-form-item label="Приближение (Zoom)">
              <el-input-number
                v-model="form.zoom"
                :min="1"
                :max="20"
                controls-position="right"
              />
            </el-form-item>

            <el-row :gutter="10">
              <el-col :span="12">
                <el-form-item label="Ключ google">
                  <el-input v-model="form.google_key" placeholder="Google API Key" />
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Маркер">
                  <div style="display: flex; align-items: center;">
                    <el-input
                      v-model="form.marker"
                      placeholder="Путь до иконки"
                      style="flex: 1;"
                    />
                    <img
                      v-if="form.marker"
                      :src="form.marker"
                      alt="Маркер"
                      style="height: 30px; margin-left: 10px; border-radius: 4px;"
                    />
                  </div>
                </el-form-item>
              </el-col>
            </el-row>

            <div class="save-button">
              <el-button type="success" @click="saveMap">Сохранить</el-button>
              <el-button>Отмена</el-button>
            </div>
          </el-form>
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
  form.value.google_tm = data.google_tm || ''
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
      google_key: form.value.google_key,
      google_tm: form.value.google_tm,
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
