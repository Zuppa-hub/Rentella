<template>
  <div class="info-grid">
    <!-- Distance -->
    <div class="info-item">
      <span class="info-label">{{ t('desktop.location.distance') }}</span>
      <p class="info-value">{{ location.distance }} km</p>
    </div>

    <!-- Number of Beaches -->
    <div class="info-item">
      <span class="info-label">{{ t('desktop.location.beachCount') }}</span>
      <p class="info-value">{{ beachCount }}</p>
    </div>

    <!-- Price Range -->
    <div class="info-item">
      <span class="info-label">{{ t('desktop.location.prices') }}</span>
      <p class="info-value">{{ location.priceRange }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Beach } from '../../services/api'

interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

const props = defineProps<{
  location: Location
  beaches: Beach[]
}>()

const { t } = useI18n()

const beachCount = computed(() => props.beaches.length)
</script>

<style scoped>
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
</style>
