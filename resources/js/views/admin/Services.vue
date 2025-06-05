<template>
  <div class="page-wrap">
    <header class="header">
      <h1>Услуги</h1>
      <RouterLink to="/services/new" class="button">Новая услуга</RouterLink>
    </header>

    <section v-if="services.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID услуги</th>
            <th>Название</th>
            <th>Видео</th>
            <th>Действие</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="service in services" :key="service.id">
            <td>{{ service.id }}</td>
            <td>{{ service.title }}</td>
            <td>
              <video
                v-if="service.video"
                :src="'/multimedia/' + service.video"
                autoplay
                loop
                muted
                playsinline
                style="max-width: 200px;"
              ></video>
            </td>
            <td>
              <RouterLink
                :to="`/services/${service.id}/edit`"
                class="icon2"
                title="Редактировать"
              >✏️</RouterLink>
              <a href="#" class="icon2" title="Удалить" @click.prevent="deleteService(service.id)">🗑</a>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <p v-else>Нет данных</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const services = ref([])

const fetchServices = async () => {
  const res = await axios.get('/api/admin/services')
  services.value = res.data.map(item => ({
    id: item.id,
    title: item.title,
    video: item.video
  }))
}

const deleteService = async (id) => {
  if (!confirm('Удалить услугу?')) return
  await axios.delete(`/api/admin/services/${id}`)
  await fetchServices()
}

onMounted(fetchServices)
</script>

<style scoped>
.page-wrap {
  padding: 20px;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
}
.data-table th,
.data-table td {
  padding: 10px;
  border-bottom: 1px solid #ddd;
}
.button {
  background: #3498db;
  color: white;
  padding: 6px 12px;
  text-decoration: none;
  margin-left: auto;
}
.icon2 {
  margin: 0 5px;
  cursor: pointer;
  text-decoration: none;
}
</style>
