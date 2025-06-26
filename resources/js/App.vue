<template>
  <router-view />
</template>

<script setup>
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

setInterval(async () => {
  try {
    await axios.get('/api/admin/me')
  } catch {
    router.push('/login')
  }
}, 1000 * 60 * 5) // каждые 5 минут
</script>
