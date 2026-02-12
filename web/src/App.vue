<template>
  <div class="home">
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
import TopBar from './components/TopBar.vue'
import MapSection, { type MapLocation } from './components/MapSection.vue'
import BottomSheet from './components/BottomSheet.vue'
import BottomNav from './components/BottomNav.vue'
import type { LocationItem } from './components/LocationCard.vue'
import { getBeaches, type Beach } from './services/api'

const authenticated = ref(false)
const error = ref<string | null>(null)
const user = ref<Record<string, unknown> | undefined>(undefined)
const searchTerm = ref('')
const isSheetCollapsed = ref(false)
let refreshTimer: number | undefined

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
    const beaches = await getBeaches()
    locations.value = (beaches || [])
      .map((beach: any) => ({
        id: beach.id,
        name: beach.name,
        distance: 23,
        priceRange: beach.min_price && beach.max_price 
          ? `${beach.min_price}-${beach.max_price}EUR` 
          : 'N/A',
        lat: beach.latitude,
        lng: beach.longitude,
      }))
      .filter((loc) => loc.lat && loc.lng)
  } catch (err) {
    console.error('Failed to load beaches:', err)
    error.value = 'Failed to load beaches'
  }
}

onMounted(() => {
  syncState()
  if (isAuthenticated()) {
    startTokenRefresh()
    loadBeaches()
  }
})

onBeforeUnmount(() => {
  stopTokenRefresh()
})

// Auto-load beaches when user authenticates
watch(authenticated, (isAuth) => {
  if (isAuth) loadBeaches()
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
