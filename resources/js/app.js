import './bootstrap'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import * as ElementPlusIconsVue from '@element-plus/icons-vue'
import 'vuefinder/dist/style.css'
import VueFinder from 'vuefinder'
import ShowreelPlayer from './components/ShowreelPlayer.vue'

const app = createApp(App)

app.use(router)
app.use(ElementPlus)

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component)
}

app.use(VueFinder, {
  request: {
    baseUrl: '/api/vuefinder',
    xsrfHeaderName: 'X-XSRF-TOKEN'
  }
})

app.component('ShowreelPlayer', ShowreelPlayer)

app.mount('#app')
