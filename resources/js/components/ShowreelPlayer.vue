<template>
  <div class="grid-row" id="showreel-grid-row">
    <a href="#"
      class="grid-item grid-item-desktop"
      :class="'grid-item-' + (media.type || 'img')"
      :data-video="videoUrl"
      :data-image="posterUrl"
      data-project-link="#"
      data-text2="<p>Showreel – видео-презентация студии</p>"
    >
      <template v-if="media.type === 'video'">
        <video preload="metadata" muted loop autoplay class="js-grid-item-media tero-lazy-load">
          <source
            v-for="(link, i) in media.links || []"
            :key="i"
            :data-src="`/multimedia/${link.link}`"
            :type="link.mime"
          />
        </video>
      </template>
      <template v-else>
        <img :data-src="posterUrl" alt="Showreel" class="js-grid-item-media tero-lazy-load" />
      </template>
      <h3 class="grid-item-title">Showreel</h3>
    </a>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const media = ref({})

const posterUrl = computed(() => {
  if (media.value.type === 'video' && media.value.poster) {
    return `/multimedia/${media.value.poster}`
  } else if (media.value.type === 'img' && media.value.link) {
    return `/multimedia/${media.value.link}`
  }
  return ''
})

const videoUrl = computed(() =>
  media.value.links?.[0]?.link ? `/multimedia/${media.value.links[0].link}` : ''
)

onMounted(async () => {
  const res = await axios.get('/api/admin/showreel')
  if (res.data?.media) {
    media.value = res.data.media

    // Принудительная инициализация lazyload,
    setTimeout(() => {
      if (window.teroLazyLoad) window.teroLazyLoad.update()
    }, 200)
  }
})
</script>
