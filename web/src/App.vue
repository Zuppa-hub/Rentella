<template>
  <div v-if="!authenticated" class="app-container">
    <WelcomeScreen @login="handleLogin" />
  </div>
  <div v-else-if="isDesktop" class="app-desktop">
    <DesktopHome :locations="locations" :initials="initials" :user-location="userLocation" />
  </div>
  <div v-else class="home">
    <TopBar :authenticated="authenticated" :initials="initials" @login="handleLogin" @logout="handleLogout" />

    <MapSection :locations="locations" selected-location="Rimini" :sheet-collapsed="isSheetCollapsed" />

    <BottomSheet
      :locations="filteredLocations"
      :total-count="filteredLocations.length"
      :search-term="searchTerm"
      :is-collapsed="isSheetCollapsed"
      @update:searchTerm="searchTerm = $event"
      @update:isCollapsed="isSheetCollapsed = $event"
    />

    <BottomNav />

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { getUser, isAuthenticated, login, logout, updateToken } from './keycloak'
import { useGeolocation } from './composables/useGeolocation'
import WelcomeScreen from './components/WelcomeScreen.vue'
import TopBar from './components/TopBar.vue'
import MapSection, { type MapLocation } from './components/MapSection.vue'
import BottomSheet from './components/BottomSheet.vue'
import BottomNav from './components/BottomNav.vue'
import DesktopHome from './components/DesktopHome.vue'
import type { LocationItem } from './components/LocationCard.vue'
import { getLocations, getBeaches, type Beach } from './services/api'

const geolocation = useGeolocation()
const { userLocation, calculateDistance, requestLocation } = geolocation

const authenticated = ref(false)
const error = ref<string | null>(null)
const user = ref<Record<string, unknown> | undefined>(undefined)
const searchTerm = ref('')
const isSheetCollapsed = ref(false)
const isDesktop = ref(false)
let refreshTimer: number | undefined
let locationDebounceTimer: number | undefined

// Cache for beaches data (loaded once at login)
const beachesCache = ref<Map<number, any[]>>(new Map())

const locations = ref<(LocationItem & MapLocation)[]>([])

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

const checkDesktop = () => {
  isDesktop.value = window.innerWidth >= 1024
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
    await logout()
  } catch (err) {
    error.value = 'Logout failed'
    console.error(err)
  }
}

const loadBeaches = async () => {
  if (!isAuthenticated()) return
  
  error.value = null
  
  try {
    const locations_data = await getLocations()
    
    // Load ALL beaches ONCE if not in cache
    if (beachesCache.value.size === 0) {
      console.log('Loading all beaches into cache...')
      try {
        const allBeaches = await getBeaches() // Load ALL beaches without filters
        console.log(`Loaded ${allBeaches.length} beaches total`)
        
        // Group beaches by location_id
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
    
    // Now process locations using cached data
    const locationsWithPrices = (locations_data || []).map((location: any) => {
      const beaches = beachesCache.value.get(location.id) || []
      
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
          ? calculateDistance(userLocation.value.lat, userLocation.value.lng, location.latitude, location.longitude)
          : Math.random() * 50,
        priceRange,
        lat: location.latitude,
        lng: location.longitude,
      }
    })
    
    locations.value = locationsWithPrices
      .filter((loc) => loc.lat && loc.lng)
      .sort((a, b) => a.distance - b.distance) // Sort by distance (closest first)
  } catch (err) {
    console.error('Failed to load locations:', err)
    error.value = 'Failed to load locations'
  }
}

onMounted(() => {
  syncState()
  checkDesktop()
  window.addEventListener('resize', checkDesktop)
  if (isAuthenticated()) {
    startTokenRefresh()
    requestLocation().catch(() => {
      console.log('Geolocation not available on mount')
    })
    loadBeaches()
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
  if (isAuth) loadBeaches()
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

</style>
