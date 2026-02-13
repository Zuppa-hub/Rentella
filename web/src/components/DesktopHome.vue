<template>
  <div class="desktop-home">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="navbar-container">
        <div class="logo-section">
          <img :src="icons.logo" :alt="t('desktop.brand.alt')" class="logo" />
        </div>
        <div class="nav-items">
          <div class="nav-item active">
            <img :src="icons.home" alt="" class="nav-icon" />
            <span>{{ t('desktop.nav.home') }}</span>
          </div>
          <div class="nav-item">
            <img :src="icons.active" alt="" class="nav-icon" />
            <span>{{ t('desktop.nav.active') }}</span>
          </div>
          <div class="nav-item">
            <img :src="icons.history" alt="" class="nav-icon" />
            <span>{{ t('desktop.nav.history') }}</span>
          </div>
          <div class="nav-item">
            <img :src="icons.settings" alt="" class="nav-icon" />
            <span>{{ t('desktop.nav.settings') }}</span>
          </div>
        </div>
        <div class="profile-section">
          <div class="profile-avatar">{{ initials }}</div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Map Section -->
      <div class="map-wrapper">
        <div ref="mapEl" class="map"></div>
        <button class="map-location-indicator" :class="{ active: props.userLocation }" @click="centerMapOnUser" :disabled="!props.userLocation">
          <span class="location-dot" :class="{ pulse: props.userLocation }"></span>
          <span class="location-text">
            {{ props.userLocation ? t('desktop.map.myLocation') : t('desktop.map.loading') }}
          </span>
        </button>
      </div>

      <!-- Sidebar -->
      <DesktopSidebar :locations="desktopLocations" :user-location="props.userLocation" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import L from 'leaflet'
import { useGeolocation } from '../composables/useGeolocation'
import type { MapLocation } from './MapSection.vue'
import DesktopSidebar from './DesktopSidebar.vue'
import logoDark from '../assets/LogoDark.svg'
import homeIcon from '../assets/icons/Home.svg'
import activeIcon from '../assets/icons/Active.svg'
import historyIcon from '../assets/icons/History.svg'
import settingsIcon from '../assets/icons/Settings.svg'
interface LocationWithMeta extends MapLocation {
  name: string
  distance: number
  priceRange: string
}

interface DesktopLocationProp {
  id: number
  name: string
  distance: number
  priceRange: string
}

const props = defineProps<{
  locations: LocationWithMeta[]
  initials: string
  userLocation?: { lat: number; lng: number } | null
}>()

const { t } = useI18n()

const mapEl = ref<HTMLDivElement | null>(null)
let map: L.Map | undefined
let markersLayer: L.LayerGroup | undefined
let userLocationMarker: L.Marker | undefined

const desktopLocations = computed<DesktopLocationProp[]>(() => {
  return props.locations.map((loc) => ({
    id: loc.id,
    name: loc.name,
    distance: loc.distance,
    priceRange: loc.priceRange,
  }))
})

const icons = {
  logo: logoDark,
  home: homeIcon,
  active: activeIcon,
  history: historyIcon,
  settings: settingsIcon,
}

const initMap = () => {
  if (!mapEl.value || map) return

  // Default view (Rimini, Italy)
  let initialLat = 44.0678
  let initialLng = 12.5695
  let initialZoom = 9

  // If we have user location, use it
  if (props.userLocation) {
    initialLat = props.userLocation.lat
    initialLng = props.userLocation.lng
    initialZoom = 13
  }

  map = L.map(mapEl.value, {
    zoomControl: false,
    attributionControl: false,
  }).setView([initialLat, initialLng], initialZoom)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(map)

  markersLayer = L.layerGroup().addTo(map)
  renderMarkers()
  renderUserLocation()
}

const renderMarkers = () => {
  if (!map || !markersLayer) return
  markersLayer.clearLayers()

  props.locations.forEach((location, index) => {
    const markerIcon = L.divIcon({
      className: 'map-pin leaflet-div-icon',
      html: `<span class="map-pin__label">${index + 1}</span>`,
      iconSize: [28, 28],
      iconAnchor: [14, 28],
      popupAnchor: [0, -28],
    })

    const marker = L.marker([location.lat, location.lng], {
      title: location.name,
      icon: markerIcon,
    })

    const popup = L.popup().setContent(
      `<div style="font-family: Inter; text-align: center;"><strong>${location.name}</strong><br/>${location.priceRange}</div>`
    )
    marker.bindPopup(popup)
    marker.addTo(markersLayer!)
  })
}

const renderUserLocation = () => {
  if (!map) return

  // Remove old user location marker
  if (userLocationMarker) {
    map.removeLayer(userLocationMarker)
  }

  // Add new user location marker if available
  if (props.userLocation) {
    const userIcon = L.divIcon({
      className: 'user-location-marker',
      html: '<div class="user-dot"></div>',
      iconSize: [16, 16],
      iconAnchor: [8, 8],
    })

    userLocationMarker = L.marker(
      [props.userLocation.lat, props.userLocation.lng],
      { icon: userIcon }
    ).addTo(map)
  }
}

const centerMapOnUser = () => {
  if (map && props.userLocation) {
    map.setView([props.userLocation.lat, props.userLocation.lng], 13, {
      animate: true,
      duration: 0.5,
    })
  }
}



onMounted(() => {
  initMap()
})

watch(
  () => props.locations,
  () => {
    renderMarkers()
  }
)

watch(
  () => props.userLocation,
  () => {
    renderUserLocation()
    // Center map on user location if changed
    if (map && props.userLocation) {
      map.setView([props.userLocation.lat, props.userLocation.lng], 13)
    }
  }
)

onBeforeUnmount(() => {
  if (map) {
    map.remove()
    map = undefined
  }
})
</script>

<style scoped>
.desktop-home {
  --color-primary: #005f6f;
  --color-primary-light: #d2eef1;
  --color-primary-light-active: #ffffff;
  --color-text-primary: #414d4f;
  --color-text-secondary: #6b7280;
  --color-bg-light: #f0f4f6;
  --color-border: #e5e7eb;
  --color-shadow: rgba(15, 23, 42, 0.08);
  --color-marker: #0b0b0b;
  --color-marker-label: #ffffff;

  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100vh;
  background: white;
}

/* Navbar Styles */
.navbar {
  height: 72px;
  background: var(--color-primary);
  box-shadow: 0px -4px 8px rgba(85, 85, 85, 0.08);
  display: flex;
  align-items: center;
  padding: 0 32px;
  flex-shrink: 0;
}

.navbar-container {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 16px;
}

.logo-section {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
}

.logo {
  height: 32px;
  width: auto;
}

.nav-items {
  display: flex;
  gap: 0;
  flex: 0 0 auto;
  justify-content: flex-end;
  margin-left: auto;
  margin-right: 12px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 16px;
  height: 100%;
  cursor: pointer;
  color: var(--color-primary-light);
  font-size: 11px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  transition: opacity 0.3s ease;
}

.nav-item:hover {
  opacity: 0.8;
}

.nav-item.active {
  color: var(--color-primary-light-active);
}

.nav-icon {
  width: 22px;
  height: 22px;
  padding: 0;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #1f2937;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
}

/* Main Content Layout */
.main-content {
  display: flex;
  flex: 1;
  gap: 0;
  overflow: hidden;
}

.map-wrapper {
  flex: 1;
  position: relative;
  background: var(--color-bg-light);
  order: 2;
  margin-left: -24px;
  z-index: 1;
}

.map {
  width: 100%;
  height: 100%;
}

/* Leaflet Map Marker Styling */
:deep(.map-pin) {
  width: 28px;
  height: 28px;
  background: var(--color-marker);
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
}

:deep(.map-pin__label) {
  display: inline-block;
  color: var(--color-marker-label);
  font-size: 13px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  transform: rotate(45deg);
}

:deep(.leaflet-marker-icon.map-pin) {
  z-index: 500;
}

/* User Location Marker */
:deep(.user-location-marker) {
  z-index: 400;
}

.user-dot {
  width: 16px;
  height: 16px;
  background: #00a8cc;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 0 0 2px #00a8cc;
}

/* Map location indicator */
.map-location-indicator {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.95);
  border: none;
  border-radius: 12px;
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #414d4f;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  z-index: 999;
  font-family: 'Inter', sans-serif;
  backdrop-filter: blur(4px);
  transition: all 0.3s ease;
  cursor: pointer;
}

.map-location-indicator:hover:not(:disabled) {
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
  transform: translateY(-1px);
}

.map-location-indicator:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.location-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #6b7280;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.location-dot.pulse {
  background: #00a8cc;
  box-shadow: 0 0 0 3px rgba(0, 168, 204, 0.2);
  animation: pulse-ring 2s infinite;
}

@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 3px rgba(0, 168, 204, 0.2);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(0, 168, 204, 0);
  }
  100% {
    box-shadow: 0 0 0 3px rgba(0, 168, 204, 0);
  }
}

.location-text {
  white-space: nowrap;
}
</style>
