<template>
  <el-card class="mb20">
    <h3>{{ title }}</h3>
    <el-form label-position="top">
      <el-form-item label="Заголовок (title)">
        <el-input v-model="model.meta_title" placeholder="Title" />
      </el-form-item>

      <el-form-item label="Описание (description)">
        <el-input
          type="textarea"
          v-model="model.meta_description"
          placeholder="Description"
          :rows="2"
        />
      </el-form-item>

      <el-form-item label="Ключевые слова (keywords)">
        <el-select
          v-model="keywords"
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
import { ref, watch } from 'vue'
import { ElNotification } from 'element-plus'

const props = defineProps({
  title: { type: String, default: 'SEO для проекта' },
  model: { type: Object, required: true }
})

const keywords = ref([])

watch(
  () => props.model.meta_keywords,
  (val) => {
    if (typeof val === 'string') {
      keywords.value = val.split(',').map(s => s.trim())
    } else if (Array.isArray(val)) {
      keywords.value = val
    }
  },
  { immediate: true }
)

const saveSeo = () => {
  props.model.meta_keywords = keywords.value.join(',')
  ElNotification({
    title: 'Успешно',
    message: 'SEO сохранится вместе с проектом',
    type: 'success'
  })
}
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
