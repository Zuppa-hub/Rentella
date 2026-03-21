<template>
  <transition name="modal-fade">
    <div v-if="isOpen && beach" class="modal-overlay" @click="handleOverlayClick">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2 class="modal-title">{{ beach.name }}</h2>
          <button class="close-button" @click="emit('close')" :title="t('common.close')">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>

        <div class="header-divider"></div>

        <div class="modal-body">
          <div class="beach-image-wrapper">
            <img v-if="beach.photo_url" :src="beach.photo_url" :alt="beach.name" class="beach-image" />
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
              <span class="detail-label">{{ t('desktop.beach.dogsAllowed') }}</span>
              <span class="detail-value">{{ dogsAllowedLabel }}</span>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-cancel" @click="emit('close')">{{ t('common.cancel') }}</button>
          <button class="btn-select" @click="emit('confirm', beach)">{{ t('desktop.common.selectBeach') }}</button>
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
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px 16px;
  z-index: 10010;
}

.modal-content {
  width: 100%;
  max-width: 760px;
  background: #f7f7f7;
  border-radius: 24px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  padding: 28px 32px 16px 32px;
}

.modal-title {
  margin: 0;
  color: #414d4f;
  font-size: 34px;
  line-height: 1.1;
  font-weight: 700;
}

.close-button {
  border: none;
  background: transparent;
  color: #414d4f;
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-divider {
  height: 1px;
  margin: 0 32px;
  background: #b9c4c6;
}

.modal-body {
  padding: 24px 32px 12px 32px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.beach-image-wrapper {
  width: 100%;
  border-radius: 8px;
  overflow: hidden;
  background: #d6dcde;
  aspect-ratio: 16 / 8;
}

.beach-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
  gap: 14px;
}

.detail-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  color: #5c6a6e;
  font-size: 16px;
}

.detail-label,
.detail-value {
  font-weight: 500;
}

.modal-footer {
  padding: 20px 32px 28px 32px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.btn-cancel,
.btn-select {
  border-radius: 12px;
  font-size: 16px;
  font-weight: 700;
  cursor: pointer;
  min-height: 52px;
  padding: 12px 24px;
}

.btn-cancel {
  border: none;
  background: transparent;
  color: #005f6f;
}

.btn-select {
  border: none;
  background: #006779;
  color: #ffffff;
  min-width: 240px;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

@media (max-width: 1024px) {
  .modal-content {
    max-width: 708px;
  }

  .modal-title {
    font-size: 34px;
  }

  .detail-row {
    font-size: 16px;
  }

  .btn-cancel,
  .btn-select {
    font-size: 16px;
    min-height: 52px;
  }

  .btn-select {
    min-width: 220px;
  }
}

@media (max-width: 640px) {
  .modal-overlay {
    padding: 16px;
  }

  .modal-content {
    border-radius: 18px;
    max-width: 100%;
  }

  .modal-header {
    padding: 20px 20px 12px 20px;
  }

  .modal-title {
    font-size: 18px;
  }

  .header-divider {
    margin: 0 20px;
  }

  .modal-body {
    padding: 16px 20px 10px 20px;
    gap: 18px;
  }

  .detail-row {
    font-size: 16px;
  }

  .modal-footer {
    padding: 12px 20px 20px 20px;
    gap: 10px;
  }

  .btn-cancel,
  .btn-select {
    min-height: 44px;
    font-size: 14px;
    border-radius: 10px;
  }

  .btn-select {
    min-width: 0;
    flex: 1;
  }

  .btn-cancel {
    flex: 1;
  }
}
</style>
