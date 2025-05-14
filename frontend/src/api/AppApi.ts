import axios from 'axios'

const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8765/'

let csrfToken = ''

type ApiResponse = {
  csrfToken: string
}

async function initializeCsrfToken() {
  const response = await AppApi.get<ApiResponse>('/api/v1/csrf-token')
  csrfToken = response.data.csrfToken
}

export const AppApi = axios.create({
  // vite.config.ts で server.proxy を定義しても反映されないのでここで URL 定義する
  baseURL,
  responseType: 'json',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
  },
})

AppApi.interceptors.request.use((config) => {
  if (csrfToken) {
    config.headers['X-CSRF-Token'] = csrfToken
  }

  return config
})

AppApi.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // vue-router を利用すると Vue の警告が出るため JS で直接リダイレクトする
      // ローカル環境だと FE と BE のポート違いで元の画面に戻れないが、本番では同じドメインなので戻れる
      window.location.href = `${baseURL}users/login?redirect=${encodeURIComponent(location.pathname)}`
    }
    return Promise.reject(error)
  },
)

initializeCsrfToken()
