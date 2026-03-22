import { getToken, login } from '../keycloak'

const API_BASE_URL = 'http://localhost:9000/api'

export interface ApiResponse<T> {
  data: T
  message?: string
}

export interface Beach {
  id: number
  name: string
  latitude: number
  longitude: number
  photo_url?: string
  allowed_animals: boolean | string | number
  owner_id: number
  location_id: number
  opening_date_id: number
  type_id: number
  city_location?: {
    id: number
    name: string
    latitude: number
    longitude: number
  }
  zones?: Array<{
    id: number
    name: string
    description?: string | null
    price_id: number
    prices?: {
      id: number
      price: number
    }
    umbrellas?: Array<{
      id: number
      number: number
    }>
  }>
  prices?: Array<{
    id: number
    price: number
    umbrella_count: number
  }>
}

export interface Location {
  id: number
  city_name: string
  latitude: number
  longitude: number
  description?: string
}

export interface BeachType {
  id: number
  type: string
}

export interface CityLocation {
  id: number
  city_name: string
  latitude: number
  longitude: number
}

export interface ZoneAvailabilityResponse {
  available: boolean
  total_umbrellas: number
  available_umbrellas: number
  first_available_umbrella_id: number | null
  price_id: number | null
}

export interface ZoneCheckoutResponse {
  order: {
    id: number
    umbrella_id: number
    start_date: string
    end_date: string
    user_id: number
    price_id: number
  }
  zone_id: number
  umbrella_id: number
}

export interface Order {
  id: number
  umbrella_id: number
  start_date: string
  end_date: string
  user_id: number
  price_id: number
  zone_id?: number
  umbrella?: {
    id: number
    number: number
    zone?: {
      id: number
      name: string
      beach?: {
        id: number
        name: string
        city_location?: {
          id: number
          city_name: string
        }
      }
    }
  }
  price?: {
    id: number
    price: number
  }
}

interface FetchOptions extends RequestInit {
  authenticated?: boolean
}

async function fetchApi<T>(
  endpoint: string,
  options: FetchOptions = {}
): Promise<T> {
  const { authenticated = true, ...fetchOptions } = options

  const url = `${API_BASE_URL}${endpoint}`
  const headers: Record<string, string> = {
    'Content-Type': 'application/json',
    ...(fetchOptions.headers as Record<string, string>),
  }

  if (authenticated) {
    const token = getToken()
    if (!token) {
      throw new Error('No authentication token available')
    }
    headers['Authorization'] = `Bearer ${token}`
  }

  const response = await fetch(url, {
    ...fetchOptions,
    headers,
  })

  if (!response.ok) {
    const error = await response.text()

    if (response.status === 401) {
      try {
        const parsed = JSON.parse(error) as { message?: string; error?: string }
        const isAuthError =
          parsed.message === 'Unauthenticated' ||
          parsed.error?.includes('Signature verification failed')

        if (isAuthError) {
          await login()
        }
      } catch {
        await login()
      }
    }

    throw new Error(`API Error: ${response.status} - ${error}`)
  }

  return response.json()
}

// Location endpoints
export async function getLocations() {
  return fetchApi<Location[]>('/locations')
}

// Beach types endpoint
export async function getBeachTypes() {
  return fetchApi<BeachType[]>('/beach-types')
}

// Beach endpoints
export async function getBeaches(filters?: { cityId?: number; allowed_animals?: string }) {
  const params = new URLSearchParams()
  if (filters?.cityId) params.append('cityId', String(filters.cityId))
  if (filters?.allowed_animals) params.append('allowed_animals', filters.allowed_animals)

  const query = params.toString() ? `?${params.toString()}` : ''
  const response = await fetchApi<Beach[]>(`/beaches${query}`)
  return response
}

export async function getBeachesByLocationId(locationId: number) {
  return fetchApi<Beach[]>(`/beaches?cityId=${locationId}`)
}

export async function getBeach(id: number) {
  return fetchApi<Beach>(`/beaches/${id}`)
}

export async function createBeach(beach: Omit<Beach, 'id'>) {
  return fetchApi<Beach>('/beaches', {
    method: 'POST',
    body: JSON.stringify(beach),
  })
}

export async function updateBeach(id: number, beach: Partial<Beach>) {
  return fetchApi<Beach>(`/beaches/${id}`, {
    method: 'PUT',
    body: JSON.stringify(beach),
  })
}

export async function deleteBeach(id: number) {
  return fetchApi<void>(`/beaches/${id}`, {
    method: 'DELETE',
  })
}

// Health check
export async function healthCheck() {
  return fetchApi('/health', { authenticated: false })
}

// City search for autocomplete
export async function searchCities(query: string, limit: number = 20): Promise<CityLocation[]> {
  const params = new URLSearchParams({ q: query, limit: String(limit) })
  return fetchApi<CityLocation[]>(`/locations/search?${params.toString()}`)
}

// User preferred location
export async function setUserPreferredLocation(locationId: number): Promise<void> {
  await fetchApi<void>('/users/preferred-location', {
    method: 'POST',
    body: JSON.stringify({ location_id: locationId }),
  })
}

export async function getUserPreferredLocation(): Promise<CityLocation | null> {
  const response = await fetchApi<{ preferred_location: CityLocation | null }>('/users/preferred-location')
  return response.preferred_location
}

// Orders availability + checkout
export async function checkZoneAvailability(payload: {
  zoneId: number
  startDate: string
  endDate: string
}): Promise<ZoneAvailabilityResponse> {
  return fetchApi<ZoneAvailabilityResponse>('/orders/availability', {
    method: 'POST',
    body: JSON.stringify({
      zone_id: payload.zoneId,
      start_date: payload.startDate,
      end_date: payload.endDate,
    }),
  })
}

export async function createZoneOrder(payload: {
  zoneId: number
  startDate: string
  endDate: string
  priceId?: number | null
}): Promise<ZoneCheckoutResponse> {
  return fetchApi<ZoneCheckoutResponse>('/orders/checkout', {
    method: 'POST',
    body: JSON.stringify({
      zone_id: payload.zoneId,
      start_date: payload.startDate,
      end_date: payload.endDate,
      ...(payload.priceId ? { price_id: payload.priceId } : {}),
    }),
  })
}

// Orders endpoints
export async function getOrders(): Promise<Order[]> {
  return fetchApi<Order[]>('/orders')
}

export async function getOrder(id: number): Promise<Order> {
  return fetchApi<Order>(`/orders/${id}`)
}

