import { ref, computed } from 'vue'
import { GEOLOCATION_TIMEOUT, DEFAULT_MAP_CENTER } from '../constants/index'
import { createLogger } from '../utils/logger'

const logger = createLogger('useGeolocation')

export interface UserLocation {
  lat: number
  lng: number
}

interface GeolocationOptions {
  timeout?: number
  enableHighAccuracy?: boolean
  maximumAge?: number
}

const isDevelopment = import.meta.env.DEV

// Validate coordinates
const validateCoordinates = (lat: number, lng: number): boolean => {
  return lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180
}

export const useGeolocation = () => {
  const userLocation = ref<UserLocation | null>(null)
  const error = ref<string | null>(null)
  const isLoading = ref(false)

  // Deduplicate concurrent requests
  let currentRequest: Promise<UserLocation> | null = null

  // Haversine distance (km)
  const calculateDistance = (
    lat1: number,
    lon1: number,
    lat2: number,
    lon2: number
  ): number => {
    if (!validateCoordinates(lat1, lon1) || !validateCoordinates(lat2, lon2)) {
      console.warn('Invalid coordinates provided to calculateDistance')
      return 0
    }

    const R = 6371
    const dLat = ((lat2 - lat1) * Math.PI) / 180
    const dLon = ((lon2 - lon1) * Math.PI) / 180

    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2)

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))

    return Math.round(R * c * 10) / 10
  }

  const requestLocation = async (
    options: GeolocationOptions = {}
  ): Promise<UserLocation> => {
    logger.debug('Requesting location', { options })
    
    if (currentRequest) {
      logger.debug('Location request already in progress, returning cached result')
      return currentRequest
    }

    if (!navigator.geolocation) {
      const message = 'Geolocation API not supported'
      error.value = message
      logger.error(message)
      throw new Error(message)
    }

    isLoading.value = true
    error.value = null

    currentRequest = new Promise<UserLocation>((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          const { latitude, longitude } = position.coords

          if (!validateCoordinates(latitude, longitude)) {
            const message = 'Invalid coordinates returned from geolocation'
            logger.error(message, { latitude, longitude })
            reject(new Error(message))
            return
          }

          const location: UserLocation = {
            lat: latitude,
            lng: longitude,
          }

          userLocation.value = location
          logger.info('Location acquired successfully', { location })
          resolve(location)
        },
        (err) => {
          const errorMessages: Record<number, string> = {
            1: 'Permission denied',
            2: 'Position unavailable',
            3: 'Timeout',
          }

          const message = errorMessages[err.code] || 'Unknown error'
          error.value = `Geolocation: ${message}`
          logger.warn(`Geolocation error (code ${err.code}):`, {'message': message})

          if (isDevelopment) {
            logger.debug('Using mock location for development')
            const mockLocation = DEFAULT_MAP_CENTER
            userLocation.value = mockLocation
            resolve(mockLocation)
          } else {
            reject(err)
          }
        },
        {
          enableHighAccuracy: options.enableHighAccuracy ?? false,
          timeout: options.timeout ?? GEOLOCATION_TIMEOUT,
          maximumAge: options.maximumAge ?? 60000, // 1 min cache
        }
      )
    })
      .finally(() => {
        isLoading.value = false
        currentRequest = null
      })

    return currentRequest
  }

  const getDistanceToLocation = (
    lat: number,
    lng: number
  ): number | null => {
    if (!userLocation.value) return null
    return calculateDistance(
      userLocation.value.lat,
      userLocation.value.lng,
      lat,
      lng
    )
  }

  const isLocationAvailable = computed(
    () => userLocation.value !== null
  )

  return {
    userLocation,
    error,
    isLoading,
    isLocationAvailable,
    requestLocation,
    calculateDistance,
    getDistanceToLocation,
  }
}