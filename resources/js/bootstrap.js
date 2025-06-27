import axios from 'axios'

window.axios = axios

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

axios.interceptors.request.use(async config => {
  const needsToken = ['post', 'put', 'patch', 'delete'].includes(config.method)

  if (needsToken) {
    // 1. Гарантируем, что кука есть
    await axios.get('/sanctum/csrf-cookie')

    // 2. Берём токен каждый раз заново
    const cookie = document.cookie
      .split('; ')
      .find(row => row.startsWith('XSRF-TOKEN='))

    if (cookie) {
      const token = decodeURIComponent(cookie.split('=')[1])
      config.headers['X-XSRF-TOKEN'] = token
    }
  }

  return config
}, error => Promise.reject(error))
