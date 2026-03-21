<template>
  <transition name="modal-fade">
    <div v-if="isOpen && beach" class="modal-overlay" @click="handleOverlayClick">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <div class="title-block">
            <h2 class="modal-title">{{ beach.name }}</h2>
            <p v-if="locationName" class="modal-subtitle">{{ locationName }}</p>
          </div>
          <button
            class="close-button"
            type="button"
            @click="emit('close')"
            :title="t('common.close')"
            :aria-label="t('common.close')"
          >
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <div class="header-divider"></div>

        <div class="modal-body">
          <div
            class="beach-image-wrapper"
            role="button"
            tabindex="0"
            :aria-label="t('desktop.common.selectBeach')"
            @click="emit('confirm', beach!)"
            @keydown.enter.prevent="emit('confirm', beach!)"
            @keydown.space.prevent="emit('confirm', beach!)"
          >
            <img v-if="beach.photo_url" :src="beach.photo_url" :alt="beach.name" class="beach-image" loading="lazy" />
            <div v-else class="beach-image-placeholder">🏖️</div>
          </div>

          <div class="details-grid">
            <div class="detail-row">
              <span class="detail-label">{{ t('desktop.beach.priceRange') }}</span>
              <span class="detail-value">{{ priceRange }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">{{ t('desktop.beach.typeLabel') }}</span>
              <span class="detail-value">{{ beachTypeLabel }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">{{ t('desktop.beach.animals') }}</span>
              <span class="detail-value">{{ dogsAllowedLabel }}</span>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-cancel" type="button" @click="emit('close')">{{ t('common.cancel') }}</button>
          <button class="btn-select" type="button" @click="emit('confirm', beach)">{{ t('desktop.common.selectBeach') }}</button>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Beach } from '../services/api'
import { isAnimalsAllowed, parseBeachTypeId } from '../utils/helpers'

type BeachViewModel = Beach & {
  min_price?: number
  max_price?: number
}

const props = defineProps<{
  isOpen: boolean
  beach: BeachViewModel | null
  beachTypes?: Record<number, string>
}>()

const emit = defineEmits<{
  close: []
  confirm: [beach: BeachViewModel]
}>()

const { t } = useI18n()

const locationName = computed(() => props.beach?.city_location?.name ?? '')

const handleOverlayClick = () => {
  emit('close')
}

const beachTypeLabel = computed(() => {
  if (!props.beach) return '-'

  const parsedId = parseBeachTypeId(props.beach.type_id)
  if (parsedId === null) {
    return t('desktop.beach.typeUnknown')
  }

  return props.beachTypes?.[parsedId] || t('desktop.beach.typeUnknown')
})

const priceRange = computed(() => {
  if (!props.beach) return '-'

  const min = props.beach.min_price
  const max = props.beach.max_price

  if (typeof min === 'number' && typeof max === 'number') {
    return `${min}-${max} €`
  }

  return '-'
})

const dogsAllowedLabel = computed(() => {
  if (!props.beach) return '-'
  return isAnimalsAllowed(props.beach.allowed_animals) ? t('desktop.beach.yes') : t('desktop.beach.no')
})
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  z-index: 2300;
}

.modal-content {
  width: min(560px, 100%);
  background: #f3f4f5;
  border-radius: 16px;
  padding: clamp(16px, 4vw, 20px);
  position: relative;
  display: flex;
  flex-direction: column;
  overflow: hidden auto;
  max-height: 92vh;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
  padding-right: 36px;
}

.title-block {
  min-width: 0;
}

.modal-title {
  margin: 0;
  color: #242b2c;
  font-size: clamp(20px, 4.6vw, 24px);
  line-height: 1.1;
  font-weight: 700;
  overflow-wrap: anywhere;
}

.modal-subtitle {
  margin: 6px 0 0;
  color: #5d6b6e;
  font-size: clamp(12px, 2.8vw, 14px);
  line-height: 1.35;
}

.close-button {
  position: absolute;
  top: 6px;
  right: 8px;
  width: 40px;
  height: 40px;
  border: 0;
  background: transparent;
  color: #4f5d61;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: background 0.2s, color 0.2s;
  flex-shrink: 0;
}

.close-button:hover {
  background: rgba(0, 0, 0, 0.08);
  color: #1f2937;
}

.close-button:active {
  background: rgba(0, 0, 0, 0.12);
  transform: scale(0.95);
}

.close-button:focus {
  outline: 2px solid #005f6f;
  outline-offset: -2px;
}

.header-divider {
  height: 1px;
  margin: 10px 0 0;
  background: #cfd8dc;
}

.modal-body {
  padding: 12px 0 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.beach-image-wrapper {
  width: 100%;
  border-radius: 10px;
  overflow: hidden;
  background: #d6dcde;
  aspect-ratio: 16 / 9;
  cursor: pointer;
  transition: transform 0.2s, filter 0.2s;
  flex-shrink: 0;
}

.beach-image-wrapper:hover {
  transform: scale(1.02);
  filter: brightness(1.05);
}

.beach-image-wrapper:active {
  transform: scale(0.98);
}

.beach-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.beach-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 48px;
}

.details-grid {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  color: #242b2c;
  font-size: clamp(14px, 3.4vw, 15px);
  padding: 12px 4px;
  border-bottom: 1px solid #d6dee0;
  transition: background 0.15s, padding 0.15s;
  cursor: default;
}

.detail-row:hover {
  background: rgba(0, 95, 111, 0.03);
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-label {
  font-weight: 600;
  color: #4f5d61;
  flex-shrink: 0;
}

.detail-value {
  font-weight: 600;
  color: #242b2c;
  text-align: right;
  flex-shrink: 0;
}

.modal-footer {
  margin-top: 16px;
  padding-top: 12px;
  border-top: 1px solid #d6dee0;
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 12px;
}

.btn-cancel,
.btn-select {
  min-width: 140px;
  height: clamp(42px, 10vw, 48px);
  padding: 12px 18px;
  border-radius: 10px;
  font-size: clamp(14px, 3.2vw, 15px);
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
  flex-shrink: 0;
}

.btn-cancel {
  border: 1px solid #d1d5db;
  background: #ffffff;
  color: #0f172a;
}

.btn-cancel:hover {
  background: #f3f4f6;
  border-color: #9ca3af;
  transform: translateY(-1px);
}

.btn-cancel:active {
  transform: translateY(0);
}

.btn-select {
  background: #005f6f;
  color: #ffffff;
}

.btn-select:hover {
  background: #004a5a;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 95, 111, 0.2);
}

.btn-select:active {
  transform: translateY(0);
  box-shadow: 0 2px 6px rgba(0, 95, 111, 0.15);
}

.btn-select:focus {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

@media (max-width: 640px) {
  .modal-overlay {
    padding: 16px;
  }

  .modal-content {
    max-height: 80vh;
  }

  .modal-header {
    padding-right: 32px;
  }

  .modal-subtitle {
    margin-top: 6px;
    font-size: 13px;
  }

  .modal-body {
    padding-top: 10px;
  }

  .modal-footer {
    flex-direction: column-reverse;
    align-items: stretch;
  }

  .btn-cancel,
  .btn-select {
    width: 100%;
    min-width: 0;
  }
}

@media (max-width: 360px) {
  .modal-content {
    padding: 14px;
  }

  .detail-row {
    padding: 8px 0;
  }
}
</style>
