export type UserListGetApiRequest = {
  sort: string | null
  page: {
    number: number
    size: number
  }
}
