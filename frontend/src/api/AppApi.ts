import axios from 'axios'

const baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8765'

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

initializeCsrfToken()
