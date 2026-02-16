<template>
  <div class="beaches-view">
    <!-- Back Button -->
    <div class="back-section">
      <button class="back-button" @click="handleBack">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        {{ t('common.back') }}
      </button>
    </div>

    <!-- Search -->
    <SearchBox v-model="searchTerm" :placeholder="t('desktop.search.placeholder')" />

    <div class="divider"></div>

    <!-- Header Info -->
    <div class="beaches-header">
      <div class="header-title">
        <h2>{{ location.name }}</h2>
        <span class="distance">
          <img :src="icons.distance" alt="" class="meta-icon" />
          {{ location.distance }} km
        </span>
        <span class="count">{{ beaches.length }} {{ t('common.beaches') }}</span>
      </div>
    </div>

    <!-- Beaches List -->
    <div class="beaches-list">
      <div
        v-for="(beach, idx) in filteredBeaches"
        :key="beach.id"
        class="beach-card"
        @click="handleSelectBeach(beach)"
      >
        <!-- Beach Image -->
        <div class="beach-image">
          <div class="image-placeholder">🏖️</div>
          <div class="beach-badge">{{ idx + 1 }}</div>
        </div>

        <!-- Beach Info -->
        <div class="beach-info">
          <h3 class="beach-name">{{ beach.name }}</h3>
          <div class="beach-details">
            <span class="detail-item" :title="t('desktop.beach.type')">
              <img :src="icons.beachType" alt="" class="detail-icon" />
              {{ getBeachTypeLabel(beach) }}
            </span>
            <span class="detail-item" :title="t('desktop.beach.animals')">
              <img :src="icons.allowedAnimals" alt="" class="detail-icon" />
              {{ isAnimalsAllowed(beach.allowed_animals) ? t('desktop.beach.yes') : t('desktop.beach.no') }}
            </span>
            <span class="detail-item" v-if="beach.min_price && beach.max_price" :title="t('common.price')">
              <img :src="icons.money" alt="" class="detail-icon" />
              €{{ beach.min_price }}-{{ beach.max_price }}
            </span>
          </div>
        </div>

        <!-- Expand Icon -->
        <div class="expand-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import type { Beach } from '../services/api'
import SearchBox from './SearchBox.vue'
import allowedAnimalsIcon from '../assets/icons/AllowedAnimals.svg'
import beachTypeIcon from '../assets/icons/BeachType.svg'
import distanceIcon from '../assets/icons/Distance.svg'
import moneyIcon from '../assets/icons/Money.svg'
import { isAnimalsAllowed, parseBeachTypeId } from '../utils/helpers'

const { t } = useI18n()

interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

type BeachViewModel = Beach & {
  min_price?: number
  max_price?: number
  has_umbrella?: boolean
}

const props = defineProps<{
  location: Location
  beaches: BeachViewModel[]
  beachTypes?: Record<number, string>
}>()

const emit = defineEmits<{
  back: []
  'select-beach': [beach: BeachViewModel]
}>()

const searchTerm = ref('')

const filteredBeaches = computed(() => {
  if (!searchTerm.value) return props.beaches
  const query = searchTerm.value.toLowerCase()
  return props.beaches.filter((beach) => beach.name.toLowerCase().includes(query))
})

const getBeachTypeLabel = (beach: BeachViewModel) => {
  const parsedId = parseBeachTypeId(beach.type_id)
  if (parsedId === null) {
    return t('desktop.beach.typeUnknown')
  }

  return props.beachTypes?.[parsedId] || t('desktop.beach.typeUnknown')
}

const handleBack = () => {
  emit('back')
}

const handleSelectBeach = (beach: BeachViewModel) => {
  emit('select-beach', beach)
}

const icons = {
  beachType: beachTypeIcon,
  allowedAnimals: allowedAnimalsIcon,
  distance: distanceIcon,
  money: moneyIcon,
}
</script>

<style scoped>
.beaches-view {
  width: 100%;
  flex: 1;
  margin-top: -67px;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Inter', sans-serif;
  border-radius: 32px 32px 0 0;
  padding: 20px clamp(12px, 4vw, 32px) calc(100px + env(safe-area-inset-bottom));
  box-shadow: 0 -2px 8px rgba(85, 85, 102, 0.18);
  min-height: calc(100vh - 80px - (64px + 204px - 67px));
  max-height: calc(100vh - 80px - (64px + 204px - 67px));
  box-sizing: border-box;
  position: relative;
  z-index: 99;
  overflow: hidden;
}

/* Back Section */
.back-section {
  padding: 0 0 12px;
}

.back-button {
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  color: #414d4f;
  font-size: 14px;
  font-weight: 500;
  padding: 8px;
  transition: color 0.2s ease;
}

.back-button:hover {
  color: #005f6f;
}

/* Search */
.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  color: #414d4f;
  outline: none;
}

.search-input::placeholder {
  color: #78898c;
}

.divider {
  height: 4px;
}

/* Beaches Header */
.beaches-header {
  padding: 0;
}

.header-title {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.header-title h2 {
  flex: 1;
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #414d4f;
}

.count {
  font-size: 12px;
  color: #78898c;
  font-weight: 400;
}

.header-meta {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

.distance {
  font-size: 12px;
  color: #78898c;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.meta-icon {
  width: 12px;
  height: 12px;
  flex-shrink: 0;
  opacity: 0.7;
}

/* Beaches List */
.beaches-list {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.beach-card {
  display: flex;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
  cursor: pointer;
  transition: all 0.2s ease;
}

.beach-card:hover {
  background: #f3f4f6;
}

/* Beach Image */
.beach-image {
  position: relative;
  width: 80px;
  height: 80px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background: #e5e7eb;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
}

.beach-badge {
  position: absolute;
  top: 4px;
  left: 4px;
  width: 28px;
  height: 28px;
  background: #ffffff;
  color: #0b0b0b;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
}

/* Beach Info */
.beach-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.beach-name {
  font-size: 16px;
  font-weight: 700;
  color: #242b2c;
  margin: 0 0 6px 0;
  letter-spacing: 0.3px;
}

.beach-details {
  display: flex;
  gap: 8px;
  font-size: 13px;
  color: #6b7280;
}

.detail-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-right: 12px;
}

.detail-icon {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  opacity: 0.7;
}

.beach-price {
  margin-top: 4px;
}

.price-range {
  font-size: 14px;
  font-weight: 600;
  color: #005f6f;
}

/* Expand Icon */
.expand-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease;
}

.beach-card:hover .expand-icon {
  color: #005f6f;
}

/* Desktop styling - sidebar layout */
@media (min-width: 1024px) {
  .beaches-view {
    width: 500px;
    min-width: 500px;
    max-width: 500px;
    flex: 0 0 500px;
    background: #fafafc;
    border-radius: 0 32px 32px 0;
    box-shadow: 8px 0px 8px rgba(136, 136, 136, 0.16);
    padding: 32px 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin: 0;
    margin-top: 0;
    overflow: hidden;
    z-index: 2;
    order: 1;
    height: auto;
    min-height: 0;
    max-height: none;
  }

  .back-section {
    padding: 0 16px;
  }

  .search-box {
    margin: 0 16px 16px 16px;
  }

  .beaches-header {
    padding: 0 16px;
  }

  .beaches-list {
    padding: 0 16px;
  }
}
</style>
