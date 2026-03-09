<template>
  <div v-if="!authenticated" class="app-container">
    <p class="auth-redirect">Redirecting to login...</p>
  </div>
  <div v-else-if="isDesktop" class="app-desktop">
    <DesktopHome v-if="!isBeachesViewOpen" :locations="locations" :initials="initials" :user-location="userLocation" :selected-location="selectedLocation" @location-click="openLocationModal" />
    <DesktopBeachesLayout
      v-if="isBeachesViewOpen && beachesViewLocation"
      :location="beachesViewLocation"
      :beaches="beachesViewBeaches"
      :beach-types="beachTypesMap"
      :initials="initials"
      :user-location="userLocation"
      @back="closeBeachesView"
      @select-beach="handleBeachSelectFromView"
    />
    <LocationModal
      :is-open="isModalOpen"
      :location="modalLocation || { id: 0, name: '', lat: 0, lng: 0, distance: 0, priceRange: 'N/A' }"
      :beaches="modalBeaches"
      @close="closeLocationModal"
      @select-beach="handleBeachSelect"
    />
  </div>
  <div v-else class="home">
    <TopBar :authenticated="authenticated" :initials="initials" @login="handleLogin" @logout="handleLogout" />

    <MapSection
      :locations="locations"
      :beaches="beachesViewBeaches"
      :use-beach-markers="isBeachesViewOpen"
      :user-location="userLocation"
      :selected-location="selectedLocation"
      :sheet-collapsed="isSheetCollapsed"
    />

    <BottomSheet
      v-if="!isBeachesViewOpen"
      :locations="filteredLocations"
      :total-count="filteredLocations.length"
      :search-term="searchTerm"
      :is-collapsed="isSheetCollapsed"
      @update:searchTerm="searchTerm = $event"
      @update:isCollapsed="isSheetCollapsed = $event"
      @location-click="openLocationModal"
    />

    <BeachesView
      v-if="isBeachesViewOpen && beachesViewLocation"
      :location="beachesViewLocation"
      :beaches="beachesViewBeaches"
      :beach-types="beachTypesMap"
      @back="closeBeachesView"
      @select-beach="handleBeachSelectFromView"
    />

    <LocationModal
      :is-open="isModalOpen"
      :location="modalLocation || { id: 0, name: '', lat: 0, lng: 0, distance: 0, priceRange: 'N/A' }"
      :beaches="modalBeaches"
      @close="closeLocationModal"
      @select-beach="handleBeachSelect"
    />

    <BottomNav />

    <p v-if="error" class="error">{{ error }}</p>
  </div>

  <div v-if="authenticated && showGeoPrompt" class="geo-auth-overlay" role="dialog" aria-modal="true" aria-labelledby="geo-auth-title" aria-describedby="geo-auth-text">
    <div class="geo-auth-card">
      <button type="button" class="geo-auth-close" aria-label="Close" @click="closeGeoPrompt" ref="geoCloseBtn">&times;</button>
      <h2 id="geo-auth-title" class="geo-auth-title">Location is needed</h2>
      <p id="geo-auth-text" class="geo-auth-text">In order to provide you with the best experience and accurate results, we need your location.</p>
      <p v-if="geoPromptError" class="geo-auth-error">{{ geoPromptError }}</p>
      <div class="geo-auth-actions">
        <button type="button" class="geo-auth-secondary" @click="handleSetLocationClick">Set Location</button>
        <button type="button" class="geo-auth-primary" :disabled="geoPromptLoading" @click="requestGeoFromPrompt">
          {{ geoPromptLoading ? 'Loading...' : 'Use Current' }}
        </button>
      </div>
    </div>
  </div>

  <SetLocationModal
    :is-open="showSetLocationModal"
    @close="handleLocationModalClose"
    @location-set="handleLocationSet"
    @use-current-location="handleUseCurrentLocationFromModal"
  />
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { getUser, isAuthenticated, login, logout, updateToken } from './keycloak'
import { useGeolocation } from './composables/useGeolocation'
import TopBar from './components/TopBar.vue'
import MapSection, { type MapLocation } from './components/MapSection.vue'
import BottomSheet from './components/BottomSheet.vue'
import BottomNav from './components/BottomNav.vue'
import DesktopHome from './components/DesktopHome.vue'
import LocationModal from './components/LocationModal.vue'
import BeachesView from './components/BeachesView.vue'
import DesktopBeachesLayout from './components/DesktopBeachesLayout.vue'
import SetLocationModal from './components/SetLocationModal.vue'
import type { LocationItem } from './components/LocationCard.vue'
import { getLocations, getBeaches, getBeachTypes, type Beach, type Location, type CityLocation } from './services/api'

const geolocation = useGeolocation()
const { userLocation, calculateDistance, requestLocation } = geolocation

// GEO-LOCATION CONSTANTS & CONFIGURATION
const GEO_CONFIG = {
  COOKIE_NAME: 'rentella_geo_after_auth',
  COOKIE_MAX_AGE: 900, // 15 minutes
  PERMISSIONS_TIMEOUT: 1000,
} as const

const authenticated = ref(false)
const error = ref<string | null>(null)
const user = ref<Record<string, unknown> | undefined>(undefined)
const searchTerm = ref('')
const isSheetCollapsed = ref(false)
const isDesktop = ref(false)
let refreshTimer: number | undefined
let locationDebounceTimer: number | undefined
const showGeoPrompt = ref(false)
const geoPromptLoading = ref(false)
const geoPromptError = ref<string | null>(null)
const geoCloseBtn = ref<HTMLButtonElement | null>(null)
const showSetLocationModal = ref(false)
let lastAuthCheckTime = 0

// Cache for beaches data (loaded once at login)
const beachesCache = ref<Map<number, any[]>>(new Map())
const locations = ref<(LocationItem & MapLocation)[]>([])

// Modal state
const isModalOpen = ref(false)
const modalLocation = ref<(LocationItem & MapLocation) | null>(null) // Data for modal display
const selectedLocation = ref<(LocationItem & MapLocation) | null>(null) // Map centering only
const modalBeaches = ref<Beach[]>([])

// Beaches View state
const isBeachesViewOpen = ref(false)
const beachesViewLocation = ref<(LocationItem & MapLocation) | null>(null)
const beachesViewBeaches = ref<Beach[]>([])
const beachTypesMap = ref<Record<number, string>>({})

const filteredLocations = computed(() => {
  if (!searchTerm.value) return locations.value
  const query = searchTerm.value.toLowerCase()
  return locations.value.filter((item) => item.name.toLowerCase().includes(query))
})

const initials = computed(() => {
  const name = String(
    user.value?.name ||
      user.value?.preferred_username ||
      user.value?.email ||
      'JD'
  )
  const parts = name.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
})

const syncState = () => {
  authenticated.value = isAuthenticated()
  user.value = getUser()
}

const checkForAuthenticationCallback = () => {
  // Check if URL contains Keycloak authentication parameters (code, state)
  const searchParams = new URLSearchParams(window.location.search)
  if (searchParams.has('code') || searchParams.has('state')) {
    // Authentication callback detected, clean URL and reload to process token
    window.history.replaceState({}, '', window.location.pathname)
    window.location.reload()
  }
}

const checkDesktop = () => {
  isDesktop.value = window.innerWidth >= 1024
}

const getCookieValue = (name: string): string | null => {
  const prefix = `${name}=`
  const cookie = document.cookie
    .split(';')
    .map((part) => part.trim())
    .find((part) => part.startsWith(prefix))
  return cookie ? decodeURIComponent(cookie.slice(prefix.length)) : null
}

const clearCookieValue = (name: string) => {
  document.cookie = `${name}=; Path=/; Max-Age=0; SameSite=Lax`
}

const checkGeoPermissionStatus = async (): Promise<'granted' | 'denied' | 'unknown'> => {
  if (!navigator.geolocation) return 'unknown'
  if (!(navigator.permissions && navigator.permissions.query)) return 'unknown'

  try {
    const permissionStatus = await Promise.race([
      navigator.permissions.query({ name: 'geolocation' }),
      new Promise<PermissionStatus>((_, reject) =>
        setTimeout(() => reject(new Error('Permission query timeout')), GEO_CONFIG.PERMISSIONS_TIMEOUT)
      ),
    ])
    return permissionStatus.state === 'granted' ? 'granted' : 'denied'
  } catch {
    return 'unknown'
  }
}

const closeGeoPrompt = () => {
  showGeoPrompt.value = false
  geoPromptError.value = null
}

const handleSetLocationClick = () => {
  showSetLocationModal.value = true
}

const handleLocationModalClose = () => {
  showSetLocationModal.value = false
}

const handleLocationSet = (location: CityLocation) => {
  // Update user location with the selected city's coordinates
  userLocation.value = {
    lat: location.latitude,
    lng: location.longitude
  }
  showGeoPrompt.value = false
  showSetLocationModal.value = false
  geoPromptError.value = null
}

const handleUseCurrentLocationFromModal = async () => {
  showSetLocationModal.value = false
  geoPromptLoading.value = true
  geoPromptError.value = null
  try {
    await requestLocation()
    showGeoPrompt.value = false
  } catch {
    geoPromptError.value = 'Unable to access location. Please check your browser permissions and try again.'
    showGeoPrompt.value = true
  } finally {
    geoPromptLoading.value = false
  }
}

const requestGeoFromPrompt = async () => {
  geoPromptLoading.value = true
  geoPromptError.value = null
  try {
    await requestLocation()
    showGeoPrompt.value = false
  } catch {
    geoPromptError.value = 'Unable to access location. Please check your browser permissions and try again.'
    showGeoPrompt.value = true
  } finally {
    geoPromptLoading.value = false
  }
}

const hydrateGeoLocationAfterAuth = async () => {
  const checkTime = Date.now()
  lastAuthCheckTime = checkTime

  const permissionState = await checkGeoPermissionStatus()
  if (checkTime !== lastAuthCheckTime) return // Race condition guard

  const shouldPrompt = getCookieValue(GEO_CONFIG.COOKIE_NAME) === '1'
  if (!shouldPrompt) return

  clearCookieValue(GEO_CONFIG.COOKIE_NAME)
  
  if (permissionState === 'granted') {
    // Auto-fetch location if already authorized
    try {
      await requestLocation()
    } catch {
      // Silent fail on auto-hydrate
    }
  } else if (permissionState !== 'unknown') {
    // Show prompt only if permission was explicitly denied or requested
    showGeoPrompt.value = true
    geoPromptError.value = null
    // Move focus to close button for accessibility
    setTimeout(() => geoCloseBtn.value?.focus(), 0)
  }
}

const startTokenRefresh = () => {
  stopTokenRefresh()
  refreshTimer = window.setInterval(() => {
    if (!isAuthenticated()) {
      stopTokenRefresh()
      return
    }
    updateToken(60).catch((err) => {
      console.error('Token refresh failed', err)
      error.value = 'Token refresh failed'
    })
  }, 30000)
}

const stopTokenRefresh = () => {
  if (refreshTimer) {
    window.clearInterval(refreshTimer)
    refreshTimer = undefined
  }
}

const handleLogin = async () => {
  error.value = null
  try {
    await login()
  } catch (err) {
    error.value = 'Login failed'
    console.error(err)
  }
}

const handleLogout = async () => {
  error.value = null
  try {
    clearCookieValue(GEO_CONFIG.COOKIE_NAME)
    await logout()
  } catch (err) {
    error.value = 'Logout failed'
    console.error(err)
  }
}

const openLocationModal = (locationId: number) => {
  const location = locations.value.find((loc) => loc.id === locationId)
  if (location) {
    // Store location for modal display, don't change map yet
    modalLocation.value = location
    modalBeaches.value = beachesCache.value.get(locationId) || []
    isModalOpen.value = true
  }
}

const closeLocationModal = () => {
  isModalOpen.value = false
  modalLocation.value = null
  modalBeaches.value = []
}

const handleBeachSelect = (beach: Beach) => {
  console.log('Beach selected:', beach)
  // TODO: Navigate to beach detail or booking page
  closeModalAndOpenBeaches()
}

const closeModalAndOpenBeaches = () => {
  if (modalLocation.value) {
    beachesViewLocation.value = modalLocation.value
    beachesViewBeaches.value = modalBeaches.value
    // NOW center the map when user clicks 'Continue'
    selectedLocation.value = modalLocation.value
    isBeachesViewOpen.value = true
    isModalOpen.value = false
    modalLocation.value = null
  }
}

const closeBeachesView = () => {
  isBeachesViewOpen.value = false
  beachesViewLocation.value = null
  beachesViewBeaches.value = []
  // Reset selected location to return to initial map view
  selectedLocation.value = null
  modalBeaches.value = []
}

const handleBeachSelectFromView = (beach: Beach) => {
  console.log('Beach selected from view:', beach)
  // TODO: Navigate to beach detail or booking page
  closeBeachesView()
}

const loadBeaches = async () => {
  if (!isAuthenticated()) return
  
  error.value = null
  
  try {
    const locations_data = await getLocations()

    const toNumber = (value: unknown) => {
      if (typeof value === 'number' && Number.isFinite(value)) return value
      const parsed = Number.parseFloat(String(value))
      return Number.isFinite(parsed) ? parsed : null
    }
    
    if (Object.keys(beachTypesMap.value).length === 0) {
      try {
        const types = await getBeachTypes()
        beachTypesMap.value = types.reduce<Record<number, string>>((acc, type) => {
          acc[type.id] = type.type
          return acc
        }, {})
      } catch (err) {
        console.error('Failed to load beach types:', err)
      }
    }

    // Load ALL beaches ONCE if not in cache
    if (beachesCache.value.size === 0) {
      console.log('Loading all beaches into cache...')
      try {
        const allBeaches = await getBeaches() // Load ALL beaches without filters
        console.log(`Loaded ${allBeaches.length} beaches total`)
        
        // Group beaches by location_id (clear and rebuild to avoid duplicates)
        beachesCache.value.clear()
        for (const beach of allBeaches) {
          if (!beachesCache.value.has(beach.location_id)) {
            beachesCache.value.set(beach.location_id, [])
          }
          beachesCache.value.get(beach.location_id)!.push(beach)
        }
        console.log('Beaches cached by location')
      } catch (err) {
        console.error('Failed to load beaches:', err)
      }
    }
    
    const uniqueLocations: Location[] = []
    const seenLocationKeys = new Set<string>()

    for (const location of locations_data || []) {
      const lat = toNumber(location.latitude)
      const lng = toNumber(location.longitude)
      const name = location.city_name ?? ''
      const key = `${name}|${lat ?? ''}|${lng ?? ''}`

      if (seenLocationKeys.has(key)) continue
      seenLocationKeys.add(key)
      uniqueLocations.push(location)
    }

    // Now process locations using cached data
    const locationsWithPrices = uniqueLocations
      .map((location) => {
        const beaches = beachesCache.value.get(location.id) || []
        const lat = toNumber(location.latitude)
        const lng = toNumber(location.longitude)

        if (lat === null || lng === null) return null
      
      // Extract min and max prices from cached beaches
      const prices = beaches
        .filter((b: any) => b.min_price && b.max_price)
        .flatMap((b: any) => [b.min_price, b.max_price])
      
      const minPrice = prices.length > 0 ? Math.min(...prices) : null
      const maxPrice = prices.length > 0 ? Math.max(...prices) : null
      const priceRange = minPrice && maxPrice ? `€${minPrice} - €${maxPrice}` : 'N/A'
      
        return {
          id: location.id,
          name: location.city_name,
          distance: userLocation.value
            ? calculateDistance(userLocation.value.lat, userLocation.value.lng, lat, lng)
            : Math.random() * 50,
          priceRange,
          lat,
          lng,
        }
      })
      .filter((loc): loc is LocationItem & MapLocation => loc !== null)

    locations.value = locationsWithPrices
      .sort((a, b) => a.distance - b.distance) // Sort by distance (closest first)
  } catch (err) {
    console.error('Failed to load locations:', err)
    error.value = 'Failed to load locations'
  }
}

onMounted(() => {
  checkForAuthenticationCallback()
  syncState()
  checkDesktop()
  window.addEventListener('resize', checkDesktop)
  if (isAuthenticated()) {
    startTokenRefresh()
    hydrateGeoLocationAfterAuth()
    loadBeaches()
  } else {
    handleLogin()
  }
})

onBeforeUnmount(() => {
  stopTokenRefresh()
  if (locationDebounceTimer) {
    window.clearTimeout(locationDebounceTimer)
  }
  window.removeEventListener('resize', checkDesktop)
})

// Auto-load beaches when user authenticates
watch(authenticated, (isAuth) => {
  if (!isAuth) {
    showGeoPrompt.value = false
    geoPromptError.value = null
    lastAuthCheckTime = 0
    return
  }

  if (isAuth) {
    loadBeaches()
    hydrateGeoLocationAfterAuth()
  }
})

// Recalculate distances when user location changes (debounced to avoid rate limiting)
watch(userLocation, () => {
  if (!authenticated.value) return
  
  // Clear previous debounce timer
  if (locationDebounceTimer) {
    window.clearTimeout(locationDebounceTimer)
  }
  
  // Debounce for 500ms to avoid too many requests
  locationDebounceTimer = window.setTimeout(() => {
    loadBeaches()
  }, 500)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');

:global(body) {
  margin: 0;
  font-family: 'Space Grotesk', system-ui, sans-serif;
  background: #0b5f6f;
  color: #0f172a;
}

:global(#app) {
  min-height: 100vh;
  background: #0b5f6f;
}

.app-desktop {
  width: 100%;
  height: 100vh;
  background: white;
}

.home {
  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  background: #0b5f6f;
  overflow: hidden;
  contain: layout style paint;
  --topbar-height: 64px;
  --nav-height: 80px;
}

:global(.leaflet-container) {
  font-family: 'Space Grotesk', system-ui, sans-serif;
}

.app-container {
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f4f7f9;
}

.geo-auth-error {
  background: #fef3f2;
  border: 1px solid #f97066;
  border-radius: 8px;
  padding: 10px 12px;
  margin: 12px 0 0;
  color: #d97706;
  font-size: 12px;
  line-height: 1.4;
}

.geo-auth-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2200;
  padding: 20px;
}

.geo-auth-card {
  width: min(720px, 100%);
  background: #f3f4f5;
  border-radius: 20px;
  padding: clamp(20px, 5vw, 28px);
  position: relative;
}

.geo-auth-close {
  position: absolute;
  top: 8px;
  right: 10px;
  width: 40px;
  height: 40px;
  border: 0;
  background: transparent;
  color: #4f5d61;
  font-size: 32px;
  line-height: 1;
  cursor: pointer;
  padding: 0;
  border-radius: 50%;
  transition: background 0.2s;
}

.geo-auth-close:hover {
  background: rgba(0, 0, 0, 0.05);
}

.geo-auth-close:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.geo-auth-title {
  margin: 0;
  font-size: clamp(22px, 5.2vw, 26px);
  font-weight: 700;
  color: #242b2c;
  line-height: 1.1;
}

.geo-auth-title::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background: #cfd8dc;
  margin-top: clamp(10px, 2.8vw, 14px);
}

.geo-auth-title::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background: #cfd8dc;
  margin-top: 14px;
}

.geo-auth-text {
  margin: 16px 0 0;
  color: #5d6b6e;
  font-size: clamp(12px, 3vw, 15px);
  line-height: 1.35;
}

.geo-auth-actions {
  margin-top: clamp(16px, 4vw, 24px);
  display: flex;
  gap: clamp(8px, 2.5vw, 12px);
}

.geo-auth-secondary,
.geo-auth-primary {
  flex: 1;
  height: clamp(44px, 11vw, 56px);
  border: 0;
  border-radius: 12px;
  font-size: clamp(12px, 3vw, 15px);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.geo-auth-secondary {
  background: transparent;
  color: #242b2c;
}

.geo-auth-secondary:hover {
  background: rgba(0, 0, 0, 0.05);
}

.geo-auth-secondary:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.geo-auth-primary {
  background: #005f6f;
  color: #fff;
}

.geo-auth-primary:hover:not(:disabled) {
  background: #095d69;
}

.geo-auth-primary:active:not(:disabled) {
  background: #064f59;
}

.geo-auth-primary:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.geo-auth-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 991px) {
  .geo-auth-card {
    max-width: 100%;
  }

  .geo-auth-close {
    width: 36px;
    height: 36px;
    font-size: 28px;
  }
}

.auth-redirect {
  margin: 0;
  color: #33515e;
  font-size: 0.95rem;
}

</style>
