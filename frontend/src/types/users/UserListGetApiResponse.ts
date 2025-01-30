export type UserListGetApiResponse = {
  data: Data[]
  meta: Meta
  links: Links
}
type Data = {
  type: string
  id: number
  attributes: Attributes
}
type Attributes = {
  name: string
  birth_day: string
  height: number
  gender: string
}
type Meta = {
  total: number
  page: Page
}
type Page = {
  number: number
  size: number
  total_size: number
}
type Links = {
  self: string
  next: string | null
  prev: string | null
}
