// User & Auth
export interface User {
  id: number
  email: string
  name: string
  created_at: string
  updated_at: string
}

export interface Owner {
  id: number
  email: string
  name: string
  phone?: string
  created_at: string
  updated_at: string
}

// Beach
export interface Beach {
  id: number
  name: string
  description?: string
  owner_id: number
  owner?: Owner
  beach_type_id: number
  beach_type?: BeachType
  city_location_id: number
  location?: CityLocation
  latitude: number
  longitude: number
  images?: BeachPicture[]
  zones?: BeachZone[]
  opening_dates?: OpeningDate[]
  created_at: string
  updated_at: string
}

export interface BeachPicture {
  id: number
  beach_id: number
  url: string
  created_at: string
  updated_at: string
}

export interface BeachType {
  id: number
  name: string
  description?: string
  icon?: string
  created_at: string
  updated_at: string
}

export interface CityLocation {
  id: number
  city: string
  region: string
  latitude: number
  longitude: number
  created_at: string
  updated_at: string
}

// Beach operations
export interface BeachZone {
  id: number
  beach_id: number
  name: string
  description?: string
  umbrellas?: Umbrella[]
  prices?: Price[]
  created_at: string
  updated_at: string
}

export interface Umbrella {
  id: number
  beach_zone_id: number
  position?: string
  available: boolean
  created_at: string
  updated_at: string
}

export interface Price {
  id: number
  beach_zone_id: number
  season?: string
  price_per_day: number
  price_per_hour?: number
  min_hours?: number
  created_at: string
  updated_at: string
}

export interface OpeningDate {
  id: number
  beach_id: number
  day_of_week: number
  open_time: string
  close_time: string
  is_closed?: boolean
  created_at: string
  updated_at: string
}

// Orders
export interface Order {
  id: number
  user_id: number
  beach_zone_id: number
  umbrella_id?: number
  start_date: string
  end_date: string
  hours?: number
  total_amount: number
  status: 'pending' | 'confirmed' | 'completed' | 'cancelled'
  created_at: string
  updated_at: string
}

// API Response
export interface ApiResponse<T> {
  data: T
  status: number
  message?: string
}

export interface ApiError {
  message: string
  status?: number
  data?: any
}

// Pagination
export interface PaginationMeta {
  total: number
  per_page: number
  current_page: number
  last_page: number
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
}
