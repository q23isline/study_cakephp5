<template>
  <div>
    <h1>Users</h1>
    <button class="btn btn-link" @click="goBack">戻る</button>
    <form class="needs-validation" novalidate>
      <div class="row mb-3">
        <label for="name" class="col-sm-2 col-form-label">氏名</label>
        <div class="col-sm-10">
          <input
            type="text"
            name="name"
            class="form-control"
            :class="{ 'is-invalid': isInvalid(`name`) }"
            id="name"
            maxlength="100"
            placeholder="田中 太郎"
            required
            v-model.trim="name"
          />
          <div class="invalid-feedback">{{ getErrorMessage(`name`) || '必須です。' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <label for="birthDay" class="col-sm-2 col-form-label">誕生日</label>
        <div class="col-sm-10">
          <input
            type="date"
            name="birthDay"
            class="form-control"
            :class="{ 'is-invalid': isInvalid(`birthDay`) }"
            id="birthDay"
            min="1900-01-01"
            required
            v-model="birthDay"
          />
          <div class="invalid-feedback">{{ getErrorMessage(`birthDay`) || '不正な値です。' }}</div>
        </div>
      </div>
      <div class="row mb-3">
        <label for="height" class="col-sm-2 col-form-label">身長 [cm]</label>
        <div class="col-sm-10">
          <input
            type="number"
            name="height"
            class="form-control"
            :class="{ 'is-invalid': isInvalid(`height`) }"
            id="height"
            min="0"
            max="330.0"
            step="0.1"
            placeholder="165.0"
            required
            v-model="height"
          />
          <div class="invalid-feedback">{{ getErrorMessage(`height`) || '必須です。' }}</div>
        </div>
      </div>
      <fieldset class="row mb-3">
        <legend class="col-form-label col-sm-2 pt-0">性別</legend>
        <div class="col-sm-10">
          <div class="form-check">
            <input
              class="form-check-input"
              :class="{ 'is-invalid': isInvalid(`gender`) }"
              type="radio"
              name="gender"
              id="man"
              value="1"
              required
              aria-describedby="genderInvalidFeedback"
              v-model="gender"
            />
            <label class="form-check-label" for="man">男性</label>
          </div>
          <div class="form-check mb-3">
            <input
              class="form-check-input"
              :class="{ 'is-invalid': isInvalid(`gender`) }"
              type="radio"
              name="gender"
              id="woman"
              value="2"
              aria-describedby="genderInvalidFeedback"
              v-model="gender"
            />
            <label class="form-check-label" for="woman">女性</label>
            <div class="invalid-feedback" id="genderInvalidFeedback">
              {{ getErrorMessage(`gender`) || '必須です。' }}
            </div>
          </div>
        </div>
      </fieldset>
      <button type="submit" class="btn btn-primary" :disabled="isSubmitting">登録</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { AppApi } from '@/api/AppApi'
import type { ValidationErrorApiResponse } from '@/types/shared/ValidationErrorApiResponse'
import type { UserSaveApiResponse } from '@/types/users/UserSaveApiResponse'
import { DateUtil } from '@/utils/DateUtil'
import { AxiosError } from 'axios'
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const name = ref()
const birthDay = ref('1990-01-01')
const height = ref()
const gender = ref('1')

const validationError = ref<ValidationErrorApiResponse>()

const isSubmitting = ref(false)

/**
 * 初期表示
 */
onMounted(async () => {
  // https://getbootstrap.jp/docs/5.3/forms/validation
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll<HTMLFormElement>('.needs-validation')

  // Loop over them and prevent submission
  forms.forEach((form) => {
    form.addEventListener(
      'submit',
      async (event: Event) => {
        event.stopPropagation()
        if (form.checkValidity()) {
          // API アクセス中に画面遷移させない
          // checkValidity より前に preventDefault するとブラウザチェック前に API アクセスされてしまう
          event.preventDefault()
          // ブラウザでバリデーション OK であれば API アクセスする
          await onSubmit()
          if (validationError.value) {
            // バリデーションエラー時、ここで remove して終わらせないとフォームがすべて✅になる
            form.classList.remove('was-validated')
            return
          }
        } else {
          event.preventDefault()
        }

        form.classList.add('was-validated')
      },
      false,
    )
  })
})

/**
 * 直前の画面に戻る
 */
const goBack = () => router.back()

/**
 * API からのバリデーションエラーを取得する
 * @param key
 */
const getError = (key: string) => {
  if (!validationError.value) {
    return null
  }

  for (const error of validationError.value.errors) {
    switch (key) {
      case 'name':
        if (error.source.pointer === '/data/attributes/name') {
          return error
        }
        break
      case 'birthDay':
        if (error.source.pointer === '/data/attributes/birth_day') {
          return error
        }
        break
      case 'height':
        if (error.source.pointer === '/data/attributes/height') {
          return error
        }
        break
      case 'gender':
        if (error.source.pointer === '/data/attributes/gender') {
          return error
        }
        break
    }
  }

  return null
}

/**
 * API からのバリデーションエラーがあるか
 * @param key
 */
const isInvalid = (key: string) => (getError(key) === null ? false : true)

/**
 * API からのバリデーションエラーメッセージを取得する
 * @param key
 */
const getErrorMessage = (key: string) => {
  const error = getError(key)
  if (error === null) {
    return ''
  }

  return error.detail
}

/**
 * 登録されたら保存しに API アクセスする
 */
const onSubmit = async () => {
  // ダブルクリックで 2重に登録されることを防ぐ
  if (isSubmitting.value) return
  isSubmitting.value = true

  const params = {
    data: {
      type: 'users',
      attributes: {
        name: name.value,
        birth_day: DateUtil.formatJpDateSlash(birthDay.value),
        height: height.value,
        gender: gender.value,
      },
    },
  }

  try {
    const response = await AppApi.post<UserSaveApiResponse>(`/api/v1/sample-users`, params)

    // TODO: 登録しました。のトーストメッセージを出したい。

    router.push(`/users/edit/${response.data.data.id}`)
  } catch (err) {
    if (err instanceof AxiosError) {
      if (err.response?.status === 400) {
        validationError.value = err.response.data
      }
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>
