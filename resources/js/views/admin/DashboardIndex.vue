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
          <el-input v-model="form.email" placeholder="Email" />
          <el-input v-model="form.tel" placeholder="Телефон" />
          <el-input v-model="form.facebook" placeholder="Facebook" />
          <el-input v-model="form.instagram" placeholder="Instagram" />
          <el-input v-model="form.linkedin" placeholder="LinkedIn" />
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
            <el-button type="success" @click="saveSettings">Сохранить</el-button>
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
  const { data } = await axios.get('/api/admin/pages/home-seo')
  form.value.seo_title = data.seo_title || ''
  form.value.seo_description = data.seo_description || ''
  form.value.seo_keywords = data.seo_keywords
    ? data.seo_keywords.split(',').map(s => s.trim())
    : []
}

const saveSettings = async () => {
  try {
    const payload = {
      seo_title: form.value.seo_title,
      seo_description: form.value.seo_description,
      seo_keywords: form.value.seo_keywords.join(',')
    }

    await axios.post('/api/admin/pages/home-seo', payload)

    ElNotification({
      title: 'Успешно',
      message: 'SEO данные обновлены',
      type: 'success',
      duration: 3000
    })
  } catch (error) {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при сохранении SEO',
      type: 'error',
      duration: 3000
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
