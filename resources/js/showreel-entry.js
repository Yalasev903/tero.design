import { createApp } from 'vue'
import ShowreelPlayer from './components/ShowreelPlayer.vue'

const app = createApp({})
app.component('ShowreelPlayer', ShowreelPlayer)
app.mount('#showreel-refresh')
