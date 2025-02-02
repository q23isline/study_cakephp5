export type UserSaveApiResponse = {
  data: Data
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
type Links = {
  self: string
}
