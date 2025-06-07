import './bootstrap'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import 'vuefinder/dist/style.css'
import VueFinder from 'vuefinder'

const app = createApp(App)

app.use(router)
app.use(ElementPlus)

app.use(VueFinder, {
  request: {
    baseUrl: '/api/vuefinder', // Laravel-роут
    xsrfHeaderName: 'X-XSRF-TOKEN'
  }
})

app.mount('#app')
