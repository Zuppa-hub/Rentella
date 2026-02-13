<template>
  <transition name="modal-fade">
    <div v-if="isOpen" class="modal-overlay" @click="handleOverlayClick">
      <div class="modal-content" @click.stop>
        <!-- Header -->
        <div class="modal-header">
          <h2 class="modal-title">{{ location.name }}</h2>
          <button class="close-button" @click="close" :title="t('common.close')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <!-- Info Grid -->
          <div class="info-grid">
            <!-- Distance -->
            <div class="info-item">
              <span class="info-label">{{ t('desktop.location.distance') }}</span>
              <p class="info-value">{{ location.distance }} km</p>
            </div>

            <!-- Number of Beaches -->
            <div class="info-item">
              <span class="info-label">{{ t('desktop.location.beachCount') }}</span>
              <p class="info-value">{{ beaches.length }}</p>
            </div>

            <!-- Price Range -->
            <div class="info-item">
              <span class="info-label">{{ t('desktop.location.prices') }}</span>
              <p class="info-value">{{ location.priceRange }}</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button class="btn-secondary" @click="close">
            {{ t('common.cancel') }}
          </button>
          <button class="btn-primary" @click="handleContinue">
            {{ t('common.continue') }}
          </button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import L from 'leaflet'
import { createLogger } from '../utils/logger'

const logger = createLogger('LocationModal')
const { t } = useI18n()

interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

interface Beach {
  id: number
  name: string
  location_id: number
  min_price?: number
  max_price?: number
  latitude?: number
  longitude?: number
}

const props = defineProps<{
  isOpen: boolean
  location: Location
  beaches: Beach[]
}>()

const emit = defineEmits<{
  close: []
  selectBeach: [beach: Beach]
}>()

const handleOverlayClick = () => {
  close()
}

const handleContinue = () => {
  logger.debug('Location confirmed', { locationId: props.location.id, locationName: props.location.name })
  emit('selectBeach', props.beaches[0] || { id: 0, name: '', location_id: props.location.id })
  close()
}

const close = () => {
  emit('close')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10000;
  padding: 16px;
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.modal-content {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  display: flex;
  flex-direction: column;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.16);
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e5e7eb;
  gap: 16px;
}

.modal-title {
  font-size: 24px;
  font-weight: 700;
  color: #0f172a;
  margin: 0;
}

.modal-distance {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

.close-button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #6b7280;
  transition: color 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-button:hover {
  color: #1f2937;
}

.modal-body {
  flex: 1;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.info-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.info-item {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  padding: 0;
  border-bottom: none;
}

.info-item:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  display: block;
  margin-bottom: 0;
}

.info-value {
  font-size: 14px;
  font-weight: 500;
  color: #0f172a;
  margin: 0;
  text-align: right;
}

.modal-footer {
  display: flex;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 0 0 16px 16px;
}

.btn-secondary,
.btn-primary {
  flex: 1;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: white;
  color: #0f172a;
}

.btn-secondary:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
}

.btn-primary {
  background: #005f6f;
  color: white;
  border: none;
}

.btn-primary:hover {
  background: #004a5a;
  transform: translateY(-1px);
}

/* Transitions */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: all 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* Mobile responsive */
@media (max-width: 640px) {
  .modal-overlay {
    padding: 16px;
  }

  .modal-content {
    max-width: 100%;
    border-radius: 16px;
  }
}
</style>
