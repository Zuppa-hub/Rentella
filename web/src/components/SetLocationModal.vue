<template>
  <div v-if="isOpen" class="location-modal-overlay" @click.self="handleClose" @keydown.esc="handleClose">
    <div class="location-modal-card">
      <button type="button" class="location-modal-close" @click="handleClose" :aria-label="t('common.close')">
        &times;
      </button>

      <h2 class="location-modal-title">{{ t('setLocation.title') }}</h2>

      <p class="location-modal-text">
        {{ t('setLocation.description') }}
      </p>

      <LocationSearchInput
        ref="searchInputComponent"
        @location-selected="handleLocationSelected"
        @use-current-location="emit('use-current-location')"
        @error="handleSearchError"
      />

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
import { setUserPreferredLocation, type CityLocation } from '../services/api'
import LocationSearchInput from './modals/LocationSearchInput.vue'

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

const searchInputComponent = ref<InstanceType<typeof LocationSearchInput> | null>(null)
const selectedLocation = ref<CityLocation | null>(null)
const error = ref<string | null>(null)
const isSaving = ref(false)

const handleLocationSelected = (location: CityLocation) => {
  selectedLocation.value = location
  error.value = null
}

const handleSearchError = (message: string) => {
  error.value = message
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
watch(
  () => props.isOpen,
  async (isOpen) => {
    if (!isOpen) {
      searchInputComponent.value?.clearSearch()
      selectedLocation.value = null
      error.value = null
    } else {
      await nextTick()
      searchInputComponent.value?.focusInput()
    }
  }
)
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
