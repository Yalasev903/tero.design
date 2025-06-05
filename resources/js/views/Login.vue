<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-gray-800 p-8 rounded-2xl shadow-xl text-white">
      <h2 class="text-2xl font-bold text-center mb-6">🔐 Вход в Админ Панель</h2>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block mb-1 text-sm text-gray-300">Email</label>
          <input
            v-model="email"
            type="email"
            placeholder="admin@tero.design"
            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            autocomplete="email"
            required
          />
        </div>

        <div>
          <label class="block mb-1 text-sm text-gray-300">Пароль</label>
          <input
            v-model="password"
            type="password"
            placeholder="••••••••"
            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            autocomplete="current-password"
            required
          />
        </div>

        <div>
          <button
            type="submit"
            class="w-full py-2 bg-blue-600 hover:bg-blue-700 transition rounded-lg font-semibold"
          >
            Войти
          </button>
        </div>
      </form>

      <p v-if="error" class="text-red-400 mt-4 text-sm text-center">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const error = ref('')
const router = useRouter()

const submit = async () => {
  error.value = ''
  try {
    await axios.get('/sanctum/csrf-cookie')
    await axios.post('/api/admin/login', {
      email: email.value,
      password: password.value
    })
    router.push('/')
  } catch (err) {
    console.error(err)
    error.value = err.response?.data?.message || 'Ошибка входа'
  }
}
</script>
