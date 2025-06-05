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
          <h3>SEO</h3>
          <el-input v-model="form.seo_title" placeholder="Title" />
          <el-input type="textarea" v-model="form.seo_description" placeholder="Description" rows="2" />
          <el-input v-model="form.seo_keywords" placeholder="Keywords (через запятую)" />
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

    <div class="save-block">
      <el-button type="primary" @click="saveSettings">💾 Сохранить</el-button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
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
  seo_keywords: '',
  poster: '',
  video: '',
  lat: '',
  lng: '',
  zoom: '',
  google_key: ''
})

const load = async () => {
  const { data } = await axios.get('/api/admin/settings')
  form.value = {
    ...form.value, // сохранить значения по умолчанию
    ...data       // подставить сохранённые из БД
  }
}

const saveSettings = async () => {
  await axios.post('/api/admin/settings', form.value)
  alert('Сохранено')
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
.save-block {
  margin-top: 20px;
}
</style>
