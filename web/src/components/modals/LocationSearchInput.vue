<template>
  <div class="location-search-section">
    <h3 class="location-search-label">{{ t('setLocation.searchLabel') }}</h3>
    <div class="location-search-wrapper">
      <div class="location-input-container">
        <input
          ref="searchInputRef"
          v-model="searchQuery"
          type="text"
          class="location-search-input"
          :placeholder="t('setLocation.searchPlaceholder')"
          @input="handleSearchInput"
          @keydown.down.prevent="navigateResults(1)"
          @keydown.up.prevent="navigateResults(-1)"
          @keydown.enter.prevent="selectHighlightedResult"
        />
        <button
          type="button"
          class="location-gps-btn"
          @click="emit('use-current-location')"
          aria-label="Use current location"
          title="Use current location"
        >
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
            <path d="M12 2V6M12 18V22M22 12H18M6 12H2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </button>
      </div>

      <!-- Autocomplete results -->
      <ul v-if="results.length > 0" class="location-results" role="listbox">
        <li
          v-for="(city, index) in results"
          :key="city.id"
          class="location-result-item"
          :class="{ 'highlighted': index === highlightedIndex }"
          role="option"
          :aria-selected="index === highlightedIndex"
          @click="selectLocation(city)"
          @mouseenter="highlightedIndex = index"
        >
          {{ city.city_name }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { searchCities, type CityLocation } from '../../services/api'
import { useDebounce } from '../../composables/useDebounce'

defineProps<{
  autoFocus?: boolean
}>()

const emit = defineEmits<{
  (e: 'location-selected', location: CityLocation): void
  (e: 'use-current-location'): void
  (e: 'error', message: string): void
}>()

const { t } = useI18n()

const searchInputRef = ref<HTMLInputElement | null>(null)
const searchQuery = ref('')
const results = ref<CityLocation[]>([])
const highlightedIndex = ref(-1)

const performSearch = async () => {
  const query = searchQuery.value.trim()
  if (query.length < 2) {
    results.value = []
    highlightedIndex.value = -1
    return
  }

  try {
    const searchResults = await searchCities(query)
    // Deduplicate by normalized city name for cleaner suggestions
    const seen = new Set<string>()
    const uniqueResults = searchResults.filter((city: CityLocation) => {
      const key = city.city_name.trim().toLowerCase()
      if (seen.has(key)) return false
      seen.add(key)
      return true
    })
    results.value = uniqueResults
    highlightedIndex.value = uniqueResults.length > 0 ? 0 : -1
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Failed to search locations. Please try again.'
    emit('error', errorMessage)
    results.value = []
    highlightedIndex.value = -1
  }
}

const { debouncedFunction: debouncedSearch } = useDebounce(performSearch, 300)

const handleSearchInput = () => {
  debouncedSearch()
}

const navigateResults = (direction: number) => {
  if (results.value.length === 0) return

  const newIndex = highlightedIndex.value + direction
  if (newIndex >= 0 && newIndex < results.value.length) {
    highlightedIndex.value = newIndex
  }
}

const selectHighlightedResult = () => {
  if (highlightedIndex.value >= 0 && results.value[highlightedIndex.value]) {
    selectLocation(results.value[highlightedIndex.value])
  }
}

const selectLocation = (city: CityLocation) => {
  emit('location-selected', city)
  searchQuery.value = city.city_name
  results.value = []
  highlightedIndex.value = -1
}

const clearSearch = () => {
  searchQuery.value = ''
  results.value = []
  highlightedIndex.value = -1
}

const focusInput = () => {
  searchInputRef.value?.focus()
}

defineExpose({ clearSearch, focusInput })
</script>

<style scoped>
.location-search-section {
  margin-top: clamp(16px, 4vw, 20px);
}

.location-search-label {
  font-size: clamp(12px, 3vw, 15px);
  font-weight: 600;
  color: #242b2c;
  margin: 0 0 10px 0;
}

.location-search-wrapper {
  position: relative;
}

.location-input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.location-search-input {
  flex: 1;
  width: 100%;
  height: clamp(44px, 11vw, 52px);
  padding: 12px 56px 12px 18px;
  border: 1px solid #cfd8dc;
  border-radius: 12px;
  font-size: clamp(14px, 3.5vw, 16px);
  color: #242b2c;
  background: #ffffff;
  transition: all 0.2s;
}

.location-search-input:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
  border-color: #005f6f;
}

.location-search-input::placeholder {
  color: #9aa5a8;
  font-size: clamp(12px, 3vw, 14px);
}

.location-gps-btn {
  position: absolute;
  right: 6px;
  width: clamp(36px, 9vw, 42px);
  height: clamp(36px, 9vw, 42px);
  border: 0;
  border-radius: 50%;
  background: transparent;
  color: #005f6f;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
}

.location-gps-btn:hover:not(:disabled) {
  background: rgba(0, 95, 111, 0.08);
}

.location-gps-btn:active:not(:disabled) {
  background: rgba(0, 95, 111, 0.15);
}

.location-gps-btn:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.location-gps-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.location-results {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: #ffffff;
  border: 1px solid #cfd8dc;
  border-radius: 12px;
  max-height: 200px;
  overflow-y: auto;
  list-style: none;
  margin: 0;
  padding: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  z-index: 10;
}

.location-result-item {
  padding: 10px 14px;
  cursor: pointer;
  border-radius: 8px;
  font-size: clamp(12px, 3vw, 15px);
  color: #242b2c;
  transition: all 0.15s;
}

.location-result-item:hover,
.location-result-item.highlighted {
  background: #f3f4f5;
  color: #005f6f;
}
</style>
