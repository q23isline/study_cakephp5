export type UserGetApiResponse = {
  data: Data
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
