/**
 * Shared type definitions for components
 */

export interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

export interface MapLocation {
  id: number
  name: string
  lat: number
  lng: number
}

export interface LocationItem {
  id: number
  name: string
  distance: number
  priceRange: string
}

export interface MarkerPoint {
  id: number
  name: string
  lat: number
  lng: number
}

export interface UserLocation {
  lat: number
  lng: number
}
