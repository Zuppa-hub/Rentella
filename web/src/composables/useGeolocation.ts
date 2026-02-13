import { ref, computed } from 'vue'

export interface UserLocation {
  lat: number
  lng: number
}

export const useGeolocation = () => {
  const userLocation = ref<UserLocation | null>(null)
  const error = ref<string | null>(null)

  // Calculate distance between two coordinates (Haversine formula)
  const calculateDistance = (lat1: number, lon1: number, lat2: number, lon2: number): number => {
    const R = 6371 // Earth radius in km
    const dLat = ((lat2 - lat1) * Math.PI) / 180
    const dLon = ((lon2 - lon1) * Math.PI) / 180
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos((lat1 * Math.PI) / 180) *
        Math.cos((lat2 * Math.PI) / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2)
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
    return Math.round(R * c * 10) / 10 // Round to 1 decimal place
  }

  // Request user location
  const requestLocation = async () => {
    console.log('Requesting location...')
    error.value = null

    if (!navigator.geolocation) {
      error.value = 'Geolocation not supported'
      console.error('Geolocation not supported')
      throw new Error('Geolocation not supported')
    }

    // If on localhost, use mock location immediately
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
      console.warn('Development environment detected - using mock location')
      const mockLocation = { lat: 44.0678, lng: 12.5695 } // Rimini
      userLocation.value = mockLocation
      error.value = null
      return mockLocation
    }

    return new Promise<UserLocation>((resolve, reject) => {
      console.log('Calling getCurrentPosition...')
      
      let completed = false

      const onSuccess = (position: GeolocationPosition) => {
        if (completed) return
        completed = true
        
        const location: UserLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        }
        console.log('✓ Location acquired:', location)
        userLocation.value = location
        error.value = null
        resolve(location)
      }

      const onError = (err: GeolocationPositionError) => {
        if (completed) return
        completed = true
        
        console.error('✗ Geolocation error code:', err.code, 'message:', err.message)
        
        // For any error (PERMISSION_DENIED=1, POSITION_UNAVAILABLE=2, TIMEOUT=3), use mock location
        console.warn(`Geolocation unavailable (code ${err.code}) - using mock location for development`)
        const mockLocation = { lat: 44.0678, lng: 12.5695 } // Rimini
        userLocation.value = mockLocation
        error.value = null
        resolve(mockLocation)
      }

      // Call getCurrentPosition with short timeout
      try {
        navigator.geolocation.getCurrentPosition(
          onSuccess,
          onError,
          {
            enableHighAccuracy: false,
            timeout: 3000, // Short timeout - 3 seconds
            maximumAge: 0,
          }
        )
      } catch (e) {
        console.error('Exception calling getCurrentPosition:', e)
        reject(e)
      }
    })
  }

  // Get distance from user to a location
  const getDistanceToLocation = (lat: number, lng: number): number | null => {
    if (!userLocation.value) return null
    return calculateDistance(userLocation.value.lat, userLocation.value.lng, lat, lng)
  }

  // Check if user location is available
  const isLocationAvailable = computed(() => userLocation.value !== null)

  return {
    userLocation,
    error,
    requestLocation,
    calculateDistance,
    getDistanceToLocation,
    isLocationAvailable,
  }
}
