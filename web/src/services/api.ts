import { getToken } from '../keycloak'

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
  allowed_animals: boolean
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
  prices?: Array<{
    id: number
    price: number
    umbrella_count: number
  }>
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
  const headers: HeadersInit = {
    'Content-Type': 'application/json',
    ...fetchOptions.headers,
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
    throw new Error(`API Error: ${response.status} - ${error}`)
  }

  return response.json()
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
