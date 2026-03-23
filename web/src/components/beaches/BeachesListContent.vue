<template>
  <template>
    <div class="back-section">
      <button class="back-button" @click="emit('back')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        {{ t('common.back') }}
      </button>
    </div>

    <SearchBox :model-value="modelValue" :placeholder="t('desktop.search.placeholder')" @update:model-value="emit('update:modelValue', $event)" />

    <div class="divider"></div>

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

    <div class="beaches-list">
      <div
        v-for="(beach, idx) in filteredBeaches"
        :key="beach.id"
        class="beach-card"
        :class="{ expanded: expandedBeachId === beach.id }"
      >
        <div class="beach-main" @click="emit('selectBeach', beach)">
          <button class="beach-image" type="button" @click.stop="emit('photoClick', beach)" :aria-label="beach.name">
            <img
              v-if="beach.photo_url"
              :src="beach.photo_url"
              :alt="beach.name"
              class="beach-photo"
              loading="lazy"
            />
            <div v-else class="image-placeholder">🏖️</div>
            <div class="beach-badge">{{ idx + 1 }}</div>
          </button>

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
              <span class="detail-item" v-if="beach.min_price && beach.max_price" :title="t('desktop.beach.priceRange')">
                <img :src="icons.money" alt="" class="detail-icon" />
                €{{ beach.min_price }}-{{ beach.max_price }}
              </span>
            </div>
          </div>

          <div class="expand-icon" :class="{ expanded: expandedBeachId === beach.id }">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </div>

        <div v-if="expandedBeachId === beach.id" class="zones-section">
          <p v-if="loadingZonesByBeach[beach.id]" class="zones-state">{{ t('desktop.beach.loadingZones') }}</p>
          <p v-else-if="zonesErrorByBeach[beach.id]" class="zones-state error">{{ zonesErrorByBeach[beach.id] }}</p>
          <p v-else-if="!(zonesByBeach[beach.id]?.length)" class="zones-state">{{ t('desktop.beach.noZones') }}</p>

          <div v-else class="zones-list">
            <div
              v-for="zone in zonesByBeach[beach.id]"
              :key="zone.id"
              class="zone-card"
              role="button"
              tabindex="0"
              @click="emit('zoneSelect', { zone, beach })"
              @keydown.enter.prevent="emit('zoneSelect', { zone, beach })"
              @keydown.space.prevent="emit('zoneSelect', { zone, beach })"
            >
              <div class="zone-content">
                <div class="zone-header">
                  <h4 class="zone-name">{{ zone.name }}</h4>
                </div>
                <div class="zone-meta">
                  <span class="detail-item" v-if="zone.price !== null">
                    <img :src="icons.money" alt="" class="detail-icon" />
                    €{{ zone.price }}
                  </span>
                  <span class="detail-item" v-if="zone.umbrellasCount !== null">
                    {{ zone.umbrellasCount }} {{ t('desktop.beach.available') }}
                  </span>
                </div>
                <p v-if="zone.description" class="zone-description">{{ zone.description }}</p>
              </div>
              <div class="zone-arrow">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import SearchBox from '../SearchBox.vue'
import { isAnimalsAllowed, parseBeachTypeId } from '../../utils/helpers'
import type { BeachViewModel, BeachZoneViewModel, Location } from '../../types/beaches'

const props = defineProps<{
  modelValue: string
  location: Location
  beaches: BeachViewModel[]
  filteredBeaches: BeachViewModel[]
  expandedBeachId: number | null
  zonesByBeach: Record<number, BeachZoneViewModel[]>
  loadingZonesByBeach: Record<number, boolean>
  zonesErrorByBeach: Record<number, string>
  beachTypes?: Record<number, string>
  icons: {
    beachType: string
    allowedAnimals: string
    distance: string
    money: string
  }
}>()

const emit = defineEmits<{
  back: []
  'update:modelValue': [value: string]
  selectBeach: [beach: BeachViewModel]
  photoClick: [beach: BeachViewModel]
  zoneSelect: [payload: { zone: BeachZoneViewModel; beach: BeachViewModel }]
}>()

const { t } = useI18n()

const getBeachTypeLabel = (beach: BeachViewModel) => {
  const parsedId = parseBeachTypeId(beach.type_id)
  if (parsedId === null) {
    return t('desktop.beach.typeUnknown')
  }

  return props.beachTypes?.[parsedId] || t('desktop.beach.typeUnknown')
}
</script>

<style scoped>
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

.divider {
  height: 4px;
}

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
  flex-direction: column;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
  transition: all 0.2s ease;
  cursor: pointer;
}

.beach-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
}

.beach-card.expanded {
  border-color: #c7d5d9;
}

.beach-main {
  display: flex;
  gap: 12px;
  cursor: pointer;
}

.beach-image {
  border: none;
  padding: 0;
  cursor: pointer;
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

.beach-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.beach-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
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

.expand-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease, transform 0.2s ease;
}

.beach-main:hover .expand-icon {
  color: #005f6f;
}

.expand-icon.expanded {
  transform: rotate(90deg);
  color: #005f6f;
}

.zones-section {
  border-top: 1px solid #e5e7eb;
  padding-top: 10px;
}

.zones-state {
  margin: 4px 0;
  font-size: 13px;
  color: #6b7280;
}

.zones-state.error {
  color: #b42318;
}

.zones-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.zone-card {
  background: #f8fafb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 12px;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  gap: 12px;
  align-items: center;
}

.zone-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

.zone-card:active {
  transform: scale(0.98);
}

.zone-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.zone-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.zone-name {
  margin: 0;
  font-size: 15px;
  color: #242b2c;
}

.zone-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease;
}

.zone-card:hover .zone-arrow {
  color: #005f6f;
}

.zone-meta {
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.zone-description {
  margin: 8px 0 0;
  color: #6b7280;
  font-size: 13px;
  line-height: 1.35;
}

@media (min-width: 1024px) {
  .back-section {
    padding: 0 16px;
  }

  :deep(.search-box) {
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
