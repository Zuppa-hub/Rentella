import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Beach, CityLocation } from '@/types'
import { apiClient } from '@/services/api'

export const useBeachStore = defineStore('beach', () => {
  const beaches = ref<Beach[]>([])
  const locations = ref<CityLocation[]>([])
  const currentBeach = ref<Beach | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const beachCount = computed(() => beaches.value.length)
  const locationCount = computed(() => locations.value.length)

  async function fetchLocations() {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get('/locations')
      locations.value = response.data.data || response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch locations'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function fetchBeaches() {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get('/beaches')
      beaches.value = response.data.data || response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch beaches'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function fetchBeachById(id: number) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get(`/beaches/${id}`)
      currentBeach.value = response.data.data || response.data
      return currentBeach.value
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch beach'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function searchBeaches(query: string, latitude?: number, longitude?: number) {
    loading.value = true
    error.value = null
    try {
      const params = new URLSearchParams({ q: query })
      if (latitude !== undefined) params.append('lat', String(latitude))
      if (longitude !== undefined) params.append('lon', String(longitude))

      const response = await apiClient.get(`/beaches?${params}`)
      beaches.value = response.data.data || response.data
    } catch (err: any) {
      error.value = err.message || 'Search failed'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  function clearError() {
    error.value = null
  }

  function reset() {
    beaches.value = []
    locations.value = []
    currentBeach.value = null
    error.value = null
  }

  return {
    beaches,
    locations,
    currentBeach,
    loading,
    error,
    beachCount,
    locationCount,
    fetchLocations,
    fetchBeaches,
    fetchBeachById,
    searchBeaches,
    clearError,
    reset,
  }
})
