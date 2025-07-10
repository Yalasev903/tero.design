<script setup>
import { ref } from 'vue'
import axios from 'axios'

const message = ref('')
const error = ref('')

const submit = async () => {
  try {
    await axios.post('/api/forgot-password') // email не передаём
    message.value = 'Ссылка отправлена на почту.'
    error.value = ''
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка'
    message.value = ''
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-900 text-white px-4">
    <div class="max-w-md w-full space-y-6 bg-gray-800 p-8 rounded-2xl shadow-xl">
      <h2 class="text-2xl font-bold text-center">🔁 Восстановление пароля</h2>
      <form @submit.prevent="submit">
        <p class="text-center text-gray-300 mb-4">Ссылка будет отправлена на dmr.ter@gmail.com</p>
        <button class="w-full mt-4 bg-blue-600 hover:bg-blue-700 py-2 rounded">Отправить ссылку</button>
      </form>
      <p v-if="message" class="text-green-400 mt-2 text-center">{{ message }}</p>
      <p v-if="error" class="text-red-400 mt-2 text-center">{{ error }}</p>
    </div>
  </div>
</template>
