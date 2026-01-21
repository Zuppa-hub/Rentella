import axios, { AxiosInstance, AxiosError, InternalAxiosRequestConfig } from 'axios'
import KeyCloakService from './KeycloakService'

/**
 * API Response interface
 */
export interface ApiResponse<T = any> {
  data: T
  status: number
  statusText?: string
}

/**
 * API Error interface
 */
export interface ApiError {
  message: string
  status?: number
  data?: any
}

/**
 * Create and configure axios instance
 */
const createApiClient = (): AxiosInstance => {
  const baseURL = import.meta.env.VITE_API_URL || 'http://localhost:9000'
  const apiPrefix = import.meta.env.VITE_API_PREFIX || '/api'

  const instance = axios.create({
    baseURL: `${baseURL}${apiPrefix}`,
    timeout: 30000,
    headers: {
      'Content-Type': 'application/json',
    },
  })

  // Request interceptor - Add auth token
  instance.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
      const token = KeyCloakService.GetAccesToken()
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    },
    (error: AxiosError) => {
      console.error('Request interceptor error:', error)
      return Promise.reject(error)
    }
  )

  // Response interceptor - Handle errors
  instance.interceptors.response.use(
    (response) => response,
    (error: AxiosError) => {
      // Handle 401 - Unauthorized (token expired)
      if (error.response?.status === 401) {
        console.warn('Authentication expired, redirecting to login')
        KeyCloakService.CallLogOut()
        // Could navigate to login here if needed
        return Promise.reject({
          message: 'Authentication expired. Please login again.',
          status: 401,
          data: error.response?.data,
        } as ApiError)
      }

      // Handle 403 - Forbidden
      if (error.response?.status === 403) {
        return Promise.reject({
          message: 'You do not have permission to access this resource',
          status: 403,
          data: error.response?.data,
        } as ApiError)
      }

      // Handle 404 - Not Found
      if (error.response?.status === 404) {
        return Promise.reject({
          message: 'Resource not found',
          status: 404,
          data: error.response?.data,
        } as ApiError)
      }

      // Handle 422 - Validation errors
      if (error.response?.status === 422) {
        return Promise.reject({
          message: 'Validation failed',
          status: 422,
          data: error.response?.data?.errors,
        } as ApiError)
      }

      // Handle 500+ - Server errors
      if (error.response?.status && error.response.status >= 500) {
        return Promise.reject({
          message: 'Server error. Please try again later.',
          status: error.response.status,
          data: error.response?.data,
        } as ApiError)
      }

      // Handle network errors
      if (!error.response) {
        return Promise.reject({
          message: 'Network error. Please check your connection.',
          data: error,
        } as ApiError)
      }

      return Promise.reject({
        message: error.message || 'An error occurred',
        status: error.response?.status,
        data: error.response?.data,
      } as ApiError)
    }
  )

  return instance
}

/**
 * API Client instance
 */
export const apiClient = createApiClient()

/**
 * Generic GET request
 */
export const apiGet = async <T = any>(url: string, params?: any): Promise<T> => {
  const response = await apiClient.get<T>(url, { params })
  return response.data
}

/**
 * Generic POST request
 */
export const apiPost = async <T = any>(url: string, data?: any): Promise<T> => {
  const response = await apiClient.post<T>(url, data)
  return response.data
}

/**
 * Generic PUT request
 */
export const apiPut = async <T = any>(url: string, data?: any): Promise<T> => {
  const response = await apiClient.put<T>(url, data)
  return response.data
}

/**
 * Generic DELETE request
 */
export const apiDelete = async <T = any>(url: string): Promise<T> => {
  const response = await apiClient.delete<T>(url)
  return response.data
}

/**
 * Generic PATCH request
 */
export const apiPatch = async <T = any>(url: string, data?: any): Promise<T> => {
  const response = await apiClient.patch<T>(url, data)
  return response.data
}

export default apiClient
