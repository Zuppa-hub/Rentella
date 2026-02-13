<template>
  <section class="map-section" :class="{ expanded: sheetCollapsed }">
    <div ref="mapEl" class="map"></div>
    <button class="location-indicator" :class="{ active: props.userLocation }" @click="centerMapOnUser" :disabled="!props.userLocation">
      <span class="location-dot" :class="{ pulse: userLocation }"></span>
    </button>
  </section>
</template>

<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref, watch } from 'vue'
import L from 'leaflet'

export type MapLocation = {
  id: number
  name: string
  lat: number
  lng: number
}

export interface UserLocation {
  lat: number
  lng: number
}

const props = defineProps<{ 
  locations: MapLocation[]
  selectedLocation: string
  sheetCollapsed: boolean
  userLocation?: UserLocation | null
}>()

const mapEl = ref<HTMLDivElement | null>(null)
let map: L.Map | undefined
let markersLayer: L.LayerGroup | undefined
let userLocationMarker: L.Marker | undefined

const initMap = () => {
  if (!mapEl.value || map) return

  // Default to Rimini, or user location if available
  let initialLat = 44.0678
  let initialLng = 12.5695
  let initialZoom = 9

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
    const marker = L.divIcon({
      className: 'map-pin',
      html: `<span>${index + 1}</span>`,
      iconSize: [34, 34],
    })
    L.marker([location.lat, location.lng], { icon: marker }).addTo(markersLayer as L.LayerGroup)
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

const destroyMap = () => {
  if (map) {
    map.remove()
    map = undefined
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
      map.setView([props.userLocation.lat, props.userLocation.lng], 13, {
        animate: true,
        duration: 0.5,
      })
    }
  }
)

onBeforeUnmount(() => {
  destroyMap()
})
</script>

<style scoped>
.map-section {
  position: relative;
  padding: 0;
  width: 100%;
  box-sizing: border-box;
  z-index: 1;
  flex: 1;
  contain: layout style paint;
}

.map {
  height: clamp(150px, 40vh, 280px);
  border-radius: 0;
  overflow: hidden;
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
  width: 100%;
  transition: height 0.4s ease-in-out;
  background: linear-gradient(135deg, #e2e8f0 0%, #d2d8e0 100%);
  will-change: height;
}

.map-section.expanded .map {
  height: calc(100vh - 64px - 80px);
}

.location-indicator {
  position: absolute;
  top: 12px;
  right: 16px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.95);
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  z-index: 999;
  cursor: pointer;
  transition: all 0.3s ease;
}

.location-indicator:hover:not(:disabled) {
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
  transform: scale(1.05);
}

.location-indicator:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.location-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #6b7280;
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

/* User Location Marker */
:deep(.user-location-marker) {
  z-index: 400;
}

.user-dot {
  width: 14px;
  height: 14px;
  background: #00a8cc;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 0 0 2px #00a8cc;
}
</style>
