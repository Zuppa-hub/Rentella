<template>
  <div 
    v-if="isOpen" 
    class="location-modal-overlay" 
    @click.self="handleClose"
    @keydown.esc="handleClose"
  >
    <div class="location-modal-card">
      <button type="button" class="location-modal-close" @click="handleClose" :aria-label="t('common.close')">
        &times;
      </button>
      
      <h2 class="location-modal-title">{{ t('setLocation.title') }}</h2>
      
      <p class="location-modal-text">
        {{ t('setLocation.description') }}
      </p>
      
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
              @click="handleUseCurrentLocation"
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
          <ul v-if="searchResults.length > 0" class="location-results" role="listbox">
            <li
              v-for="(city, index) in searchResults"
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
      
      <p v-if="error" class="location-error">{{ error }}</p>
      
      <div class="location-modal-actions">
        <button type="button" class="location-btn-secondary" @click="handleClose">
          {{ t('setLocation.back') }}
        </button>
        <button
          type="button"
          class="location-btn-primary"
          @click="handleDone"
          :disabled="!selectedLocation || isSaving"
        >
          {{ isSaving ? t('setLocation.saving') : t('setLocation.done') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import { useI18n } from 'vue-i18n'
import { searchCities, setUserPreferredLocation, type CityLocation } from '../services/api'
import { useDebounce } from '../composables/useDebounce'

interface Props {
  isOpen: boolean
}

interface Emits {
  (e: 'close'): void
  (e: 'location-set', location: CityLocation): void
  (e: 'use-current-location'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const { t } = useI18n()

const searchInputRef = ref<HTMLInputElement | null>(null)
const searchQuery = ref('')
const searchResults = ref<CityLocation[]>([])
const selectedLocation = ref<CityLocation | null>(null)
const error = ref<string | null>(null)
const isSaving = ref(false)
const highlightedIndex = ref(-1)

const performSearch = async () => {
  const query = searchQuery.value.trim()
  if (query.length < 2) {
    searchResults.value = []
    highlightedIndex.value = -1
    return
  }

  error.value = null

  try {
    const results = await searchCities(query)
    // Deduplicate by normalized city name for cleaner suggestions
    const seen = new Set<string>()
    const uniqueResults = results.filter((city) => {
      const key = city.city_name.trim().toLowerCase()
      if (seen.has(key)) return false
      seen.add(key)
      return true
    })
    searchResults.value = uniqueResults
    highlightedIndex.value = uniqueResults.length > 0 ? 0 : -1
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Failed to search locations. Please try again.'
    error.value = errorMessage
    searchResults.value = []
    highlightedIndex.value = -1
  }
}

const { debouncedFunction: debouncedSearch } = useDebounce(performSearch, 300)

const handleSearchInput = () => {
  debouncedSearch()
}

const navigateResults = (direction: number) => {
  if (searchResults.value.length === 0) return
  
  const newIndex = highlightedIndex.value + direction
  if (newIndex >= 0 && newIndex < searchResults.value.length) {
    highlightedIndex.value = newIndex
  }
}

const selectHighlightedResult = () => {
  if (highlightedIndex.value >= 0 && searchResults.value[highlightedIndex.value]) {
    selectLocation(searchResults.value[highlightedIndex.value])
  }
}

const selectLocation = (city: CityLocation) => {
  selectedLocation.value = city
  searchQuery.value = city.city_name
  searchResults.value = []
  highlightedIndex.value = -1
  error.value = null
}

const handleUseCurrentLocation = () => {
  emit('use-current-location')
}

const handleDone = async () => {
  if (!selectedLocation.value) return

  isSaving.value = true
  error.value = null

  try {
    await setUserPreferredLocation(selectedLocation.value.id)
    emit('location-set', selectedLocation.value)
    emit('close')
  } catch (err) {
    const errorMessage = err instanceof Error ? err.message : 'Failed to save location. Please try again.'
    error.value = errorMessage
  } finally {
    isSaving.value = false
  }
}

const handleClose = () => {
  emit('close')
}

// Reset state when modal closes and focus input when it opens
watch(() => props.isOpen, async (isOpen) => {
  if (!isOpen) {
    searchQuery.value = ''
    searchResults.value = []
    selectedLocation.value = null
    error.value = null
    highlightedIndex.value = -1
  } else {
    await nextTick()
    searchInputRef.value?.focus()
  }
})
</script>

<style scoped>
.location-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2300;
  padding: 20px;
}

.location-modal-card {
  width: min(720px, 100%);
  background: #f3f4f5;
  border-radius: 20px;
  padding: clamp(20px, 5vw, 28px);
  position: relative;
}

.location-modal-close {
  position: absolute;
  top: 8px;
  right: 10px;
  width: 40px;
  height: 40px;
  border: 0;
  background: transparent;
  color: #4f5d61;
  font-size: 32px;
  line-height: 1;
  cursor: pointer;
  padding: 0;
  border-radius: 50%;
  transition: background 0.2s;
}

.location-modal-close:hover {
  background: rgba(0, 0, 0, 0.05);
}

.location-modal-close:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.location-modal-title {
  margin: 0;
  font-size: clamp(22px, 5.2vw, 26px);
  font-weight: 700;
  color: #242b2c;
  line-height: 1.1;
}

.location-modal-title::after {
  content: '';
  display: block;
  width: 100%;
  height: 1px;
  background: #cfd8dc;
  margin-top: 14px;
}

.location-modal-text {
  margin: 16px 0 0;
  color: #5d6b6e;
  font-size: clamp(12px, 3vw, 15px);
  line-height: 1.35;
}

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

.location-error {
  background: #fef3f2;
  border: 1px solid #f97066;
  border-radius: 8px;
  padding: 10px 14px;
  margin: 14px 0 0 0;
  color: #d97706;
  font-size: clamp(11px, 2.8vw, 13px);
  line-height: 1.4;
}

.location-modal-actions {
  margin-top: clamp(16px, 4vw, 24px);
  display: flex;
  gap: clamp(8px, 2.5vw, 12px);
}

.location-btn-secondary,
.location-btn-primary {
  flex: 1;
  height: clamp(44px, 11vw, 56px);
  border: 0;
  border-radius: 12px;
  font-size: clamp(12px, 3vw, 15px);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.location-btn-secondary {
  background: transparent;
  color: #242b2c;
}

.location-btn-secondary:hover {
  background: rgba(0, 0, 0, 0.05);
}

.location-btn-secondary:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.location-btn-primary {
  background: #005f6f;
  color: #fff;
}

.location-btn-primary:hover:not(:disabled) {
  background: #095d69;
}

.location-btn-primary:active:not(:disabled) {
  background: #064f59;
}

.location-btn-primary:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.location-btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 991px) {
  .location-modal-card {
    max-width: 100%;
  }

  .location-modal-close {
    width: 36px;
    height: 36px;
    font-size: 28px;
  }
}
</style>
