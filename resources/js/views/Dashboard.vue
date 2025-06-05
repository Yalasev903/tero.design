<template>
  <div>
    <h1>Вы в админке!</h1>
    <button @click="logout">Выйти</button>
  </div>
</template>

<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const logout = async () => {
  await axios.post('/api/admin/logout')
  router.push('/login')
}
</script>
