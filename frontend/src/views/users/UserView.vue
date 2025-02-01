<template>
  <div>
    <h1>Users</h1>
    <button class="btn btn-link" @click="goBack">戻る</button>
    <ul class="nav justify-content-end">
      <li class="nav-item">
        <RouterLink class="nav-link link-primary" :to="`/users/edit/${props.userId}`"
          >編集</RouterLink
        >
      </li>
      <li class="nav-item">
        <button type="button" class="btn btn-danger" @click="deleteEntity(props.userId)">
          削除
        </button>
      </li>
    </ul>
    <div class="row row-cols-md-1">
      <div class="col">
        <div class="card">
          <img src="/favicon.ico" class="card-img-top" alt="favicon" />
          <div class="card-body">
            <h5 class="card-title">{{ data?.data.attributes.name }} ({{ data?.data.id }})</h5>
            <p class="card-text">誕生日 : {{ data?.data.attributes.birth_day }}</p>
            <p class="card-text">身長 : {{ data?.data.attributes.height }}</p>
            <p class="card-text">性別 : {{ data?.data.attributes.gender }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AppApi } from '@/api/AppApi'
import type { UserGetApiResponse } from '@/types/users/UserGetApiResponse'
import { AxiosError } from 'axios'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const props = defineProps({
  userId: Number,
})

const router = useRouter()

const data = ref<UserGetApiResponse>()

/**
 * 初期表示
 */
onMounted(async () => await load())

/**
 * 直前の画面に戻る
 */
const goBack = () => router.back()

/**
 *削除
 * @param id
 */
const deleteEntity = async (id: number | undefined) => {
  if (id == null) {
    return
  }
  const isConfirmed = window.confirm(`削除してもよろしいですか？ ID: ${id}`)
  if (isConfirmed) {
    try {
      await AppApi.delete<void>(`/api/v1/sample-users/${id}`)

      // TODO: 削除しました。のトーストメッセージを出したい。

      router.push('/users')
    } catch (err) {
      if (err instanceof AxiosError) {
        if (err.response?.status === 404) {
          // TODO: すでに削除されています。のトーストメッセージを出したい。
        }
      }
    }
  }
}

/**
 * API アクセスして読み込み
 */
const load = async () => {
  try {
    const response = await AppApi.get<UserGetApiResponse>(`/api/v1/sample-users/${props.userId}`)
    data.value = response.data
  } catch (err) {
    if (err instanceof AxiosError) {
      if (err.response?.status === 404) {
        // TODO: 404ページに飛ばす。ページ作ってないので現状はホームに
        router.push(`/`)
      }
    }
  }
}
</script>
