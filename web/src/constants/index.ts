/**
 * Application Constants
 * Centralized configuration for production deployment
 */

// View & Layout
export const BREAKPOINT_DESKTOP = 1024

// Timers (milliseconds)
export const TOKEN_REFRESH_INTERVAL = 30000 // 30 seconds
export const LOCATION_DEBOUNCE_DELAY = 500 // 500ms
export const GEOLOCATION_TIMEOUT = 15000 // 15 seconds
export const API_TIMEOUT = 10000 // 10 seconds
export const API_RETRY_ATTEMPTS = 3
export const API_RETRY_DELAY = 1000 // 1 second

// Geolocation
export const DEFAULT_MAP_CENTER = { lat: 44.0678, lng: 12.5695 } // Rimini
export const DEFAULT_MAP_ZOOM = 9
export const USER_LOCATION_ZOOM = 13

// API
export const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:9000/api'
export const API_ENDPOINTS = {
  LOCATIONS: '/locations',
  BEACHES: '/beaches',
  USERS: '/users',
  ORDERS: '/orders',
} as const

// Debugging
export const DEBUG_MODE = import.meta.env.DEV
export const LOG_LEVEL = import.meta.env.DEV ? 'debug' : 'warn' // 'debug' | 'info' | 'warn' | 'error'

// User Session
export const SESSION_STORAGE_KEY = 'rentella_session'
export const CACHE_VERSION = 'v1'

// Error Messages
export const ERROR_MESSAGES = {
  GEOLOCATION_NOT_SUPPORTED: 'Geolocation is not supported by your browser',
  GEOLOCATION_PERMISSION_DENIED: 'Location permission was denied',
  GEOLOCATION_POSITION_UNAVAILABLE: 'Location information is unavailable',
  GEOLOCATION_TIMEOUT: 'Location request timed out',
  API_NETWORK_ERROR: 'Network error. Please check your connection',
  API_SERVER_ERROR: 'Server error. Please try again later',
  API_UNAUTHORIZED: 'Your session has expired. Please log in again',
  LOGIN_FAILED: 'Login failed. Please try again',
  LOGOUT_FAILED: 'Logout failed. Please try again',
  DATA_LOAD_FAILED: 'Failed to load data. Please try again',
} as const

// Success Messages
export const SUCCESS_MESSAGES = {
  LOGIN_SUCCESS: 'Logged in successfully',
  LOGOUT_SUCCESS: 'Logged out successfully',
} as const

// Animation Durations
export const ANIMATION_DURATION = {
  MAP_PAN: 500, // milliseconds
  TRANSITION: 300, // milliseconds
} as const
