<template>
  <div>
    <h1>Users</h1>
    <div class="container">
      <div class="row">
        <div class="col text-end">
          <RouterLink class="link-primary" to="/users/add">新規追加</RouterLink>
        </div>
      </div>
      <p>合計: {{ data?.meta.total }} 人</p>
      <div class="row g-3 mb-3">
        <div class="col form-floating">
          <input
            type="search"
            class="form-control"
            id="floatingSearchKeyword"
            placeholder="氏名を検索"
            v-model="searchKeyword"
            @change="changeCondition"
          />
          <label for="floatingSearchKeyword">氏名を検索</label>
        </div>
        <div class="col-auto form-floating">
          <select
            class="form-select"
            id="floatingSelectSort"
            aria-label="表示順"
            v-model="selectedSort"
            @change="changeCondition"
          >
            <option></option>
            <option value="name">氏名 昇順 ↑</option>
            <option value="-name">氏名 降順 ↓</option>
            <option value="birth_day">誕生日 昇順 ↑</option>
            <option value="-birth_day">誕生日 降順 ↓</option>
            <option value="height">身長 昇順 ↑</option>
            <option value="-height">身長 降順 ↓</option>
            <option value="gender">性別 昇順 ↑</option>
            <option value="-gender">性別 降順 ↓</option>
          </select>
          <label for="floatingSelectSort">表示順</label>
        </div>
        <div class="col-auto form-floating">
          <select
            class="form-select"
            id="floatingSelectLimit"
            aria-label="表示件数"
            v-model="selectedLimit"
            @change="changeCondition"
          >
            <option value="10">10 件</option>
            <option value="20">20 件</option>
            <option value="50">50 件</option>
          </select>
          <label for="floatingSelectLimit">表示件数</label>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <table class="table">
            <thead>
              <tr>
                <th scope="col">ID</th>
                <th scope="col">氏名</th>
                <th scope="col">誕生日</th>
                <th scope="col">身長 [cm]</th>
                <th scope="col">性別</th>
                <th scope="col"></th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in data?.data" :key="user.id">
                <th class="align-middle" scope="row">
                  <RouterLink class="link-primary" :to="`/users/${user.id}`">{{
                    user.id
                  }}</RouterLink>
                </th>
                <td class="align-middle">{{ user.attributes.name }}</td>
                <td class="align-middle">{{ user.attributes.birth_day }}</td>
                <td class="align-middle">{{ user.attributes.height }}</td>
                <td class="align-middle">{{ user.attributes.gender }}</td>
                <td class="align-middle">
                  <RouterLink class="link-primary" :to="`/users/edit/${user.id}`">編集</RouterLink>
                </td>
                <td class="align-middle">
                  <button type="button" class="btn btn-danger" @click="deleteRow(user.id)">
                    削除
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: isDisabledPrevious() }">
                <a
                  class="page-link"
                  :href="isDisabledPrevious() ? undefined : '#'"
                  aria-label="Previous"
                  @click="load(data?.links.prev)"
                >
                  <span aria-hidden="true">&laquo;</span>
                </a>
              </li>
              <li class="page-item active" aria-current="page">
                <a class="page-link">{{ data?.meta.page.number }}</a>
              </li>
              <li class="page-item" :class="{ disabled: isDisabledNext() }">
                <a
                  class="page-link"
                  :href="isDisabledNext() ? undefined : '#'"
                  aria-label="Next"
                  @click="load(data?.links.next)"
                >
                  <span aria-hidden="true">&raquo;</span>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AppApi } from '@/api/AppApi'
import type { UserListGetApiRequest } from '@/types/users/UserListGetApiRequest'
import type { UserListGetApiResponse } from '@/types/users/UserListGetApiResponse'
import { AxiosError } from 'axios'
import { ref, onMounted, watch } from 'vue'
import { useRouter, useRoute, type LocationQueryValue } from 'vue-router'

const router = useRouter()
const route = useRoute()

/**
 * URL のクエリパラメータの値から配列を除いて数値か文字列型のみにする
 * @param query
 */
const getQueryRemoveArray = (query: string | number | LocationQueryValue | LocationQueryValue[]) =>
  Array.isArray(query)
    ? query.pop() // 配列の場合、最後の要素を取得
    : query

/**
 * URL のクエリパラメータの値を数値型に変換する
 * @param query
 */
const getValueAsNumber = (query: string | number | LocationQueryValue | LocationQueryValue[]) => {
  const value = getQueryRemoveArray(query)
  if (typeof value === 'number') {
    return value
  } else if (typeof value === 'string') {
    return parseInt(value)
  }

  return value
}

/**
 * URL のクエリパラメータの値を文字列型に変換する
 * @param query
 */
const getValueAsString = (query: string | number | LocationQueryValue | LocationQueryValue[]) => {
  const value = getQueryRemoveArray(query)
  if (typeof value === 'number') {
    return value.toString()
  } else if (typeof value === 'string') {
    return value
  }

  return value
}

const searchKeyword = ref(getValueAsString(route.query['filter[name]']) || null)
let currentPageNumber = getValueAsNumber(route.query['page[number]']) || 1
const selectedSort = ref(getValueAsString(route.query['sort']) || null)
const selectedLimit = ref(getValueAsNumber(route.query['page[size]']) || 10)

const data = ref<UserListGetApiResponse>()

/**
 * 初期表示
 */
onMounted(async () => await addParamsLoad())

/**
 * クエリパラメータの値が変わったら検索条件のテキストボックスやプルダウンにも反映する
 */
watch(
  () => route.query['filter[name]'],
  (newVal) => {
    searchKeyword.value = getValueAsString(newVal) || null
  },
)
watch(
  () => route.query['page[number]'],
  (newVal) => {
    currentPageNumber = getValueAsNumber(newVal) || 1
  },
)
watch(
  () => route.query['sort'],
  (newVal) => {
    selectedSort.value = getValueAsString(newVal) || null
  },
)
watch(
  () => route.query['page[size]'],
  (newVal) => {
    selectedLimit.value = getValueAsNumber(newVal) || 10
  },
)

/**
 * 表示順や表示件数が変わったら API アクセス
 */
const changeCondition = async () => await addParamsLoad()

/**
 * ページングの戻るを非活性にするか
 */
const isDisabledPrevious = () => data.value?.links.prev === null

/**
 * ページングの進むを非活性にするか
 */
const isDisabledNext = () => data.value?.links.next === null

/**
 *1行削除
 * @param id
 */
const deleteRow = async (id: number) => {
  const isConfirmed = window.confirm(`削除してもよろしいですか？ ID: ${id}`)
  if (isConfirmed) {
    try {
      await AppApi.delete<void>(`/api/v1/sample-users/${id}`)

      await load(data.value?.links.self)
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
 * 検索条件を組み立てて API アクセス
 */
const addParamsLoad = async () => {
  const params = {
    filter: {
      name: searchKeyword.value,
    },
    sort: selectedSort.value,
    page: {
      number: currentPageNumber,
      size: selectedLimit.value,
    },
  }

  load('/api/v1/sample-users', params)
}

/**
 * API アクセスして読み込み
 * @param link
 * @param params
 */
const load = async (
  link: string | null = '/api/v1/sample-users',
  params: UserListGetApiRequest | null = null,
) => {
  if (link === null) {
    return
  }

  try {
    const response = await AppApi.get<UserListGetApiResponse>(link, {
      params: params,
    })
    data.value = response.data
    currentPageNumber = response.data.meta.page.number

    // URL に検索クエリパラメータを反映する
    router.push({
      query: {
        ...route.query, // 今のクエリパラメータの値を失わないように引き継ぐ
        'filter[name]': searchKeyword.value || undefined, // undefined にすると値がないときクエリパラメータのキーも消してくれる
        'page[number]': currentPageNumber,
        sort: selectedSort.value || undefined,
        'page[size]': selectedLimit.value,
      },
    })
  } catch (err) {
    if (err instanceof AxiosError) {
      if (err.response?.status === 400) {
        // TODO: 悪意を持ってパラメータを変えているので 404ページに飛ばす。ページ作ってないので現状はホームに
        router.push(`/`)
      }
    }
  }
}
</script>
