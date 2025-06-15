<template>
  <div>
    <h2>Создание нового проекта</h2>
    <el-form :model="form" label-position="top" @submit.prevent="submit">
      <el-form-item label="Название проекта">
        <el-input v-model="form.title" />
      </el-form-item>
      <el-form-item label="Описание (text1)">
        <el-input type="textarea" v-model="form.text1" />
      </el-form-item>
      <el-form-item label="Описание для главной (text2)">
        <el-input type="textarea" v-model="form.text2" />
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submit">Создать</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { ElNotification } from 'element-plus'

const form = ref({
  title: '',
  text1: '',
  text2: '',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  multimedia_grid: [],
})

const submit = async () => {
  try {
    await axios.post('/api/admin/projects', form.value)
    ElNotification({ title: 'Готово', message: 'Проект создан', type: 'success' })
  } catch (error) {
    ElNotification({ title: 'Ошибка', message: 'Ошибка при создании', type: 'error' })
  }
}
</script>
