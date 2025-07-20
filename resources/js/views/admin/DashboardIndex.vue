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
              <el-input v-model="form.behance" placeholder="Ссылка на Behance" :size="inputSize" />
            </el-form-item>
            <el-form-item label="Ссылка на Facebook">
              <el-input v-model="form.facebook" placeholder="Ссылка на Facebook" :size="inputSize" />
            </el-form-item>
            <el-form-item label="Ссылка на Instagram">
              <el-input v-model="form.instagram" placeholder="Ссылка на Instagram" :size="inputSize" />
            </el-form-item>
            <el-form-item label="Ссылка на LinkedIn">
              <el-input v-model="form.linkedin" placeholder="Ссылка на LinkedIn" :size="inputSize" />
            </el-form-item>
            <!-- <el-form-item label="Ссылка на Pinterest">
              <el-input v-model="form.pinterest" placeholder="Ссылка на Pinterest" :size="inputSize" />
            </el-form-item> -->
            <el-form-item label="Ссылка на YouTube">
              <el-input v-model="form.youtube" placeholder="Ссылка на YouTube" :size="inputSize" />
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
              <el-input v-model="form.jivochat_id" placeholder="JivoChat ID" :size="inputSize" />
            </el-form-item>
            <el-form-item label="Email">
              <el-input v-model="form.email" placeholder="Email" :size="inputSize" />
            </el-form-item>
            <!-- <el-form-item label="Телефон">
              <el-input v-model="form.tel" placeholder="Телефон" :size="inputSize" />
            </el-form-item> -->
            <el-form-item label="Google Tag Manager">
              <el-input
                v-model="form.google_tm"
                type="textarea"
                :rows="3"
                :size="inputSize"
                placeholder="Google Tag Manager (gtag.js)"
              />
            </el-form-item>

            <el-form-item label="Подпись слева (в футере) Контакты">
            <el-input v-model="form.footer_left" placeholder="Пример: POLAND, Kraków" :size="inputSize" />
            </el-form-item>
            <el-form-item label="Подпись справа (в футере) Контакты">
            <el-input v-model="form.footer_right" placeholder="Пример: SWITZERLAND, Geneve ..." :size="inputSize" />
            </el-form-item>
            <el-form-item label="Подпись справа (второй ряд) Контакты">
            <el-input v-model="form.footer_right_note" placeholder="Пример: OAKS GROUP SA, Associate Partner" :size="inputSize" />
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
              <el-input v-model="form.seo_title" placeholder="Title" :size="inputSize"/>
            </el-form-item>
            <el-form-item label="Описание (description)">
              <el-input
                type="textarea"
                v-model="form.seo_description"
                :size="inputSize"
                placeholder="Description"
                :rows="2"
              />
            </el-form-item>
            <el-form-item label="Ключевые слова (keywords)" :size="inputSize">
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
              <el-input v-model="form.lat" placeholder="Широта" :size="inputSize"/>
            </el-form-item>
            <el-form-item label="Координаты Lng">
              <el-input v-model="form.lng" placeholder="Долгота" :size="inputSize"/>
            </el-form-item>
            <el-form-item label="Приближение (Zoom)" :size="inputSize">
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
                  <el-input v-model="form.google_key" placeholder="Google API Key" :size="inputSize"/>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item label="Маркер">
                <div style="display: flex; align-items: center;">
                    <el-button type="primary" @click="openMarkerManager" :size="inputSize">Выбрать файл</el-button>
                    <span v-if="form.marker" style="margin-left: 10px; max-width: 220px; overflow: hidden; text-overflow: ellipsis;">
                    {{ form.marker }}
                    </span>
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

    <!-- ViewFinder -->
    <teleport to="body">
    <div v-if="showMarkerFinder" class="finder-modal">
        <div class="finder-container">
        <vue-finder
            id="vuefinder-marker"
            :request="{
            baseUrl: '/api/vuefinder',
            adapter: 'local',
            xsrfHeaderName: 'X-XSRF-TOKEN'
            }"
            @select="handleMarkerSelect"
        />
        <button class="close-btn" @click="showMarkerFinder = false">✖</button>
        </div>
    </div>
    </teleport>
</template>

<script setup>
defineOptions({
  name: 'DashboardIndex'
})
import { ref, onMounted, inject } from 'vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'
import ShowreelPreview from '@/components/admin/ShowreelPreview.vue'

const showMarkerFinder = ref(false)

const openMarkerManager = () => {
  showMarkerFinder.value = true
}

const handleMarkerSelect = (items) => {
  if (!items.length) return
  const path = items[0].path.replace(/^local:\/\//, '').replace(/^multimedia\//, '')
  form.value.marker = `/multimedia/${path}`
  showMarkerFinder.value = false
}

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
  google_key: '',
  marker: '',
  footer_left: '',
  footer_right: '',
  footer_right_note: ''
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
  form.value.marker = data.marker || ''
  form.value.google_tm = data.google_tm || ''
  form.value.footer_left = data.footer_left || ''
  form.value.footer_right = data.footer_right || ''
  form.value.footer_right_note = data.footer_right_note || ''
}

const inputSize = inject('inputSize')

const selectMarker = () => {
  // Открываем VueFinder
  window.open('/admin/filemanager?type=image', 'filemanager', 'width=1000,height=600')

  // Назначаем callback
  window.setSelectedFilePath = (path) => {
    // Убираем префикс "/storage" если нужно
    form.value.marker = path.startsWith('/') ? path : '/' + path
  }
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
      footer_left: form.value.footer_left,
      footer_right: form.value.footer_right,
      footer_right_note: form.value.footer_right_note,
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
      google_key: form.value.google_key,
      marker: form.value.marker,
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
