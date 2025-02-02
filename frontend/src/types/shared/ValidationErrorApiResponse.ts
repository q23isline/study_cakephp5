export type ValidationErrorApiResponse = {
  errors: ErrorItem[]
}
type ErrorItem = {
  title: string
  source: {
    pointer: string
  }
  detail: string
}
