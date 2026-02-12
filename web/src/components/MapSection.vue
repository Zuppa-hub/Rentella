<template>
  <section class="map-section" :class="{ expanded: sheetCollapsed }">
    <div ref="mapEl" class="map"></div>
    <button class="location-selector">
      <span class="location-icon"></span>
      {{ selectedLocation }}
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

const props = defineProps<{ 
  locations: MapLocation[]
  selectedLocation: string
  sheetCollapsed: boolean
}>()

const mapEl = ref<HTMLDivElement | null>(null)
let map: L.Map | undefined
let markersLayer: L.LayerGroup | undefined

const initMap = () => {
  if (!mapEl.value || map) return

  map = L.map(mapEl.value, {
    zoomControl: false,
    attributionControl: false,
  }).setView([44.0678, 12.5695], 9)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(map)

  markersLayer = L.layerGroup().addTo(map)
  renderMarkers()
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

.location-selector {
  position: absolute;
  top: 12px;
  right: 16px;
  background: rgba(255, 255, 255, 0.92);
  border: none;
  border-radius: 12px;
  padding: 8px 12px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
  color: #32424b;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  z-index: 999;
}

.location-icon {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #0b5f6f;
  position: relative;
}

.location-icon::after {
  content: '';
  position: absolute;
  inset: 2px;
  background: #0b5f6f;
  border-radius: 50%;
}
</style>
