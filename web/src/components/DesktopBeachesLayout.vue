<template>
  <div class="desktop-beaches-layout">
    <BeachesDesktopNavbar :initials="initials" current-tab="home" @navigate="emit('navigate', $event)" />

    <!-- Main Content -->
    <div class="main-content">
      <!-- Map Section -->
      <div class="map-wrapper">
        <div ref="mapEl" class="map"></div>
        <button class="map-location-indicator" :class="{ active: userLocation }" @click="centerMapOnUser" :disabled="!userLocation">
          <span class="location-dot" :class="{ pulse: userLocation }"></span>
          <span class="location-text">
            {{ userLocation ? t('desktop.map.myLocation') : t('desktop.map.loading') }}
          </span>
        </button>
      </div>

      <!-- Beaches View Sidebar -->
      <BeachesView
        :location="location"
        :beaches="beaches"
        :expand-beach-id="expandBeachId"
        :beach-types="beachTypes"
        @back="emit('back')"
        @select-beach="emit('select-beach', $event)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import L from 'leaflet'
import BeachesView from './BeachesView.vue'
import BeachesDesktopNavbar from './beaches/BeachesDesktopNavbar.vue'
import { toNumber } from '../utils/helpers'
import type { BeachViewModel, Location } from '../types/beaches'

const props = defineProps<{
  location: Location
  beaches: BeachViewModel[]
  expandBeachId?: number | null
  beachTypes?: Record<number, string>
  initials: string
  userLocation?: { lat: number; lng: number } | null
}>()

const emit = defineEmits<{
  back: []
  'select-beach': [beach: BeachViewModel]
  navigate: [tab: string]
}>()

const { t } = useI18n()

const mapEl = ref<HTMLDivElement | null>(null)
let map: L.Map | undefined
let markersLayer: L.LayerGroup | undefined
let userLocationMarker: L.Marker | undefined

const initMap = () => {
  if (!mapEl.value || map) return

  // Always center on the selected location, not user location
  const initialLat = props.location.lat
  const initialLng = props.location.lng
  const initialZoom = 13

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

  // Only show beach markers (numbered dots), not location marker
  props.beaches.forEach((beach, idx) => {
    const lat = toNumber(beach.latitude)
    const lng = toNumber(beach.longitude)

    if (lat !== null && lng !== null) {
      let markerLat = lat
      let markerLng = lng

      if (idx > 0) {
        const angle = idx * 1.25
        const radius = 0.00018 * Math.sqrt(idx)
        markerLat += Math.cos(angle) * radius
        markerLng += Math.sin(angle) * radius
      }

      const beachIcon = L.divIcon({
        className: 'map-pin leaflet-div-icon',
        html: `<span class="map-pin__label">${idx + 1}</span>`,
        iconSize: [28, 28],
        iconAnchor: [14, 28],
      })

      L.marker([markerLat, markerLng], {
        icon: beachIcon,
        title: beach.name,
      }).addTo(markersLayer!)
    }
  })
}

const renderUserLocation = () => {
  if (!map || !props.userLocation) return

  if (userLocationMarker) {
    userLocationMarker.remove()
  }

  const userIcon = L.divIcon({
    className: 'user-location-marker',
    html: '<div class="user-dot"></div>',
    iconSize: [20, 20],
    iconAnchor: [10, 10],
  })

  userLocationMarker = L.marker([props.userLocation.lat, props.userLocation.lng], {
    icon: userIcon,
    title: 'Your location',
  }).addTo(map)
}

const centerMapOnUser = () => {
  if (map && props.userLocation) {
    map.setView([props.userLocation.lat, props.userLocation.lng], 13, {
      animate: true,
    })
  }
}

const destroyMap = () => {
  if (map) {
    map.remove()
    map = undefined
  }
}

onMounted(() => {
  setTimeout(() => initMap(), 100)
})

watch(
  () => props.beaches,
  () => {
    renderMarkers()
  },
  { deep: true }
)

watch(
  () => props.userLocation,
  () => {
    renderUserLocation()
  },
  { deep: true }
)

onBeforeUnmount(() => {
  destroyMap()
})
</script>

<style scoped>
.desktop-beaches-layout {
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

  width: 100%;
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: white;
}

/* Main Content */
.main-content {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.map-wrapper {
  flex: 1;
  position: relative;
  overflow: hidden;
  order: 2;
  margin-left: -24px;
  background: #f0f4f6;
  z-index: 1;
}

.map {
  width: 100%;
  height: 100%;
}

.map-location-indicator {
  position: absolute;
  top: 16px;
  right: 16px;
  background: white;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 14px;
  font-weight: 500;
  color: #0f172a;
}

.map-location-indicator:hover:not(:disabled) {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transform: translateY(-1px);
}

.map-location-indicator:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.location-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #9ca3af;
  transition: background 0.3s ease;
}

.location-dot.pulse {
  background: #005f6f;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

/* Map markers */
:deep(.map-pin-location) {
  z-index: 400;
}

:deep(.location-marker) {
  width: 32px;
  height: 32px;
  background: #005f6f;
  border: 3px solid white;
  border-radius: 50%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

/* Beach Markers - Same style as homepage with numbered dots */
:deep(.map-pin) {
  width: 28px;
  height: 28px;
  background: #1f2937;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
}

:deep(.map-pin__label) {
  display: inline-block;
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  transform: rotate(45deg);
}

:deep(.leaflet-marker-icon.map-pin) {
  z-index: 500;
}

:deep(.user-location-marker) {
  z-index: 401;
}

:deep(.user-dot) {
  width: 20px;
  height: 20px;
  background: #3b82f6;
  border: 3px solid white;
  border-radius: 50%;
  box-shadow: 0 0 0 2px #3b82f6, 0 2px 6px rgba(0, 0, 0, 0.3);
}
</style>
