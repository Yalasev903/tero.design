<template>
  <div class="min-h-screen bg-gray-900 text-white flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-gray-800 p-8 rounded-2xl shadow-xl">
      <h2 class="text-2xl font-bold text-center mb-4">🔑 Новый пароль</h2>
      <form @submit.prevent="submit">
        <input v-model="password" type="password" placeholder="Новый пароль"
               class="w-full mb-4 px-4 py-2 rounded text-black" />
        <input v-model="password_confirmation" type="password" placeholder="Подтвердите пароль"
               class="w-full mb-4 px-4 py-2 rounded text-black" />
        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded">
          Сменить пароль
        </button>
        <p v-if="message" class="text-green-400 mt-2 text-center">{{ message }}</p>
        <p v-if="error" class="text-red-400 mt-2 text-center">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const password = ref('')
const password_confirmation = ref('')
const message = ref('')
const error = ref('')
const token = ref('')

const fixedEmail = 'dmr.ter@gmail.com'

onMounted(() => {
  token.value = route.query.token || ''
})

const submit = async () => {
  try {
    await axios.post('/api/reset-password', {
      email: fixedEmail,
      token: token.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
    message.value = 'Пароль успешно изменён!'
    error.value = ''
    setTimeout(() => router.push('/login'), 2000)
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка сброса'
    message.value = ''
  }
}
</script>

