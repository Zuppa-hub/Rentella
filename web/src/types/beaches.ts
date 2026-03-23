import type { Beach } from '../services/api'

export interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

export type BeachViewModel = Beach & {
  min_price?: number
  max_price?: number
  has_umbrella?: boolean
  photo_url?: string
}

export interface BeachZoneViewModel {
  id: number
  name: string
  description: string | null
  umbrellasCount: number | null
  price: number | null
  priceId: number | null
}
