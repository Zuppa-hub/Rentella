<template>
  <div class="desktop-home">
    <DesktopHomeNavbar :initials="props.initials" active-tab="home" @navigate="emit('navigate', $event)" />

    <!-- Main Content -->
    <div class="main-content">
      <!-- Map Section -->
      <div class="map-wrapper">
        <div ref="mapEl" class="map"></div>
        <MapLocationIndicator :location="props.userLocation" @center-map="centerMapOnUser" />
      </div>

      <!-- Sidebar -->
      <DesktopSidebar
        :locations="desktopLocations"
        :user-location="props.userLocation"
        @location-click="emit('location-click', $event)"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import L from 'leaflet'
import type { MapLocation } from './MapSection.vue'
import DesktopSidebar from './DesktopSidebar.vue'
import DesktopHomeNavbar from './home/DesktopHomeNavbar.vue'
import MapLocationIndicator from './home/MapLocationIndicator.vue'
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
  selectedLocation?: LocationWithMeta | null
}>()

const emit = defineEmits<{
  (event: 'location-click', locationId: number): void
  (event: 'navigate', tab: string): void
}>()

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

    userLocationMarker = L.marker([props.userLocation.lat, props.userLocation.lng], { icon: userIcon }).addTo(
      map
    )
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

// Watch for selected location changes and center map
watch(
  () => props.selectedLocation,
  (newLocation) => {
    if (map && newLocation) {
      map.setView([newLocation.lat, newLocation.lng], 13, {
        animate: true,
      })
    }
  }
)
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
</style>
