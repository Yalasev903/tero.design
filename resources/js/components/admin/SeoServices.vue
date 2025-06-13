<template>
  <el-card class="mb20">
    <h3>{{ title }}</h3>
    <el-form label-position="top">
      <el-form-item label="Заголовок (title)">
        <el-input v-model="model.seo_title" placeholder="Title" />
      </el-form-item>

      <el-form-item label="Описание (description)">
        <el-input
          type="textarea"
          v-model="model.seo_description"
          placeholder="Description"
          :rows="2"
        />
      </el-form-item>

      <el-form-item label="Ключевые слова (keywords)">
        <el-select
          v-model="model.seo_keywords"
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
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { ElNotification } from 'element-plus'
import axios from 'axios'

const props = defineProps({
  title: { type: String, default: 'SEO' },
  pageName: { type: String, required: true }
})

const model = ref({
  seo_title: '',
  seo_description: '',
  seo_keywords: []
})

const loadSeo = async () => {
  try {
    const res = await axios.get(`/api/admin/pages/${props.pageName}-seo`)
    model.value.seo_title = res.data.seo_title || ''
    model.value.seo_description = res.data.seo_description || ''
    model.value.seo_keywords = res.data.seo_keywords
      ? res.data.seo_keywords.split(',').map(s => s.trim())
      : []
  } catch (e) {
    ElNotification({
      title: 'Ошибка',
      message: 'Ошибка при загрузке SEO',
      type: 'error'
    })
  }
}

const saveSeo = async () => {
  try {
    await axios.post(`/api/admin/pages/${props.pageName}-seo`, {
      seo_title: model.value.seo_title,
      seo_description: model.value.seo_description,
      seo_keywords: Array.isArray(model.value.seo_keywords)
        ? model.value.seo_keywords.join(',')
        : model.value.seo_keywords
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

onMounted(loadSeo)
</script>

<style scoped>
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
