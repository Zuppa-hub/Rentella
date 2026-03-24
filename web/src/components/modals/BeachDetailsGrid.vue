<template>
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
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { BeachViewModel } from '../../types/beaches'
import { isAnimalsAllowed, parseBeachTypeId } from '../../utils/helpers'

const props = defineProps<{
  beach: BeachViewModel
  beachTypes?: Record<number, string>
}>()

const { t } = useI18n()

const beachTypeLabel = computed(() => {
  const parsedId = parseBeachTypeId(props.beach.type_id)
  if (parsedId === null) {
    return t('desktop.beach.typeUnknown')
  }

  return props.beachTypes?.[parsedId] || t('desktop.beach.typeUnknown')
})

const priceRange = computed(() => {
  const min = props.beach.min_price
  const max = props.beach.max_price

  if (typeof min === 'number' && typeof max === 'number') {
    return `${min}-${max} €`
  }

  return '-'
})

const dogsAllowedLabel = computed(() => {
  return isAnimalsAllowed(props.beach.allowed_animals) ? t('desktop.beach.yes') : t('desktop.beach.no')
})
</script>

<style scoped>
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
  transition:
    background 0.15s,
    padding 0.15s;
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
</style>
