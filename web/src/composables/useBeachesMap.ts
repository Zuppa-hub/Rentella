import { type Ref } from 'vue'
import L from 'leaflet'
import { toNumber } from '../utils/helpers'
import type { BeachViewModel, Location } from '../types/beaches'

type UserLocation = { lat: number; lng: number } | null | undefined

export const useBeachesMap = (options: {
  mapEl: Ref<HTMLDivElement | null>
  getLocation: () => Location
  getBeaches: () => BeachViewModel[]
  getUserLocation: () => UserLocation
}) => {
  let map: L.Map | undefined
  let markersLayer: L.LayerGroup | undefined
  let userLocationMarker: L.Marker | undefined

  const renderMarkers = () => {
    if (!map || !markersLayer) return
    const layer = markersLayer
    layer.clearLayers()

    options.getBeaches().forEach((beach, idx) => {
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
        }).addTo(layer)
      }
    })
  }

  const renderUserLocation = () => {
    if (!map) return

    const userLocation = options.getUserLocation()
    if (!userLocation) return

    if (userLocationMarker) {
      userLocationMarker.remove()
    }

    const userIcon = L.divIcon({
      className: 'user-location-marker',
      html: '<div class="user-dot"></div>',
      iconSize: [20, 20],
      iconAnchor: [10, 10],
    })

    userLocationMarker = L.marker([userLocation.lat, userLocation.lng], {
      icon: userIcon,
      title: 'Your location',
    }).addTo(map)
  }

  const initMap = () => {
    if (!options.mapEl.value || map) return

    const location = options.getLocation()

    map = L.map(options.mapEl.value, {
      zoomControl: false,
      attributionControl: false,
    }).setView([location.lat, location.lng], 13)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
    }).addTo(map)

    markersLayer = L.layerGroup().addTo(map)
    renderMarkers()
    renderUserLocation()
  }

  const centerMapOnUser = () => {
    const userLocation = options.getUserLocation()
    if (map && userLocation) {
      map.setView([userLocation.lat, userLocation.lng], 13, {
        animate: true,
      })
    }
  }

  const destroyMap = () => {
    if (map) {
      map.remove()
      map = undefined
      markersLayer = undefined
      userLocationMarker = undefined
    }
  }

  return {
    initMap,
    renderMarkers,
    renderUserLocation,
    centerMapOnUser,
    destroyMap,
  }
}
