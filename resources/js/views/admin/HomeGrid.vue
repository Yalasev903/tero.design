<template>
  <div>
    <div class="home-grid-panel">
      <h3>🏠 Home Grid</h3>
      <p>Здесь будет управление медиа-сеткой (preview, drag & drop и т.д.)</p>
      <!-- тут будет сетка -->
    </div>

    <!-- SEO блок для Home Grid -->
    <SeoIndex
      v-model="form"
      title="SEO Home Grid"
      :apiUrl="'/api/admin/pages/homegrid-seo'"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import SeoIndex from '@/components/admin/SeoIndex.vue'
import axios from 'axios'

const form = ref({
  seo_title: '',
  seo_description: '',
  seo_keywords: []
})

const loadSeo = async () => {
  const res = await axios.get('/api/admin/pages/homegrid-seo')
  form.value.seo_title = res.data.seo_title || ''
  form.value.seo_description = res.data.seo_description || ''
  form.value.seo_keywords = res.data.seo_keywords
    ? res.data.seo_keywords.split(',').map(s => s.trim())
    : []
}

onMounted(loadSeo)
</script>

<style scoped>
.home-grid-panel {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  margin-bottom: 30px;
}
</style>
