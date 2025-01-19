<template>
  <div class="about">
    <h1>This is an about page {{ data?.data.attributes.name }}</h1>
  </div>
</template>

<style>
@media (min-width: 1024px) {
  .about {
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
}
</style>

<script setup lang="ts">
import { AppApi } from '@/api/AppApi'
import { ref, onMounted } from 'vue'
type ApiResponse = {
  data: Data
}
type Data = {
  type: string
  id: number
  attributes: Attributes
}
type Attributes = {
  name: string
  birth_date: string
  height: number
  gender: string
}

const data = ref<ApiResponse>()
const loading = ref(true)
const error = ref<string | null>(null)
onMounted(async () => {
  try {
    const response = await AppApi.get<ApiResponse>('/api/v1/sample-users/1')
    data.value = response.data
  } catch (err) {
    error.value = (err as Error).message
  } finally {
    loading.value = false
  }
})
</script>
