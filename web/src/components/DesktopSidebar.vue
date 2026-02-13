<template>
  <aside class="sidebar">
    <div class="search-box">
      <svg viewBox="0 0 24 24" class="search-icon">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none" />
        <path d="M20 20l-4-4" stroke="currentColor" stroke-width="2" fill="none" />
      </svg>
      <input
        v-model="searchTerm"
        type="text"
        :placeholder="t('desktop.search.placeholder')"
        class="search-input"
      />
    </div>

    <div class="divider"></div>

    <div class="locations-header">
      <h2>{{ t('desktop.locations.title') }}</h2>
      <span class="count">
        {{ t('desktop.locations.count', { count: filteredLocations.length }) }}
      </span>
    </div>

    <div class="locations-list">
      <div
        v-for="(location, idx) in filteredLocations"
        :key="location.id"
        class="location-card"
        @click="emit('location-click', location.id)"
      >
        <div class="card-content">
          <div class="location-name">
            <div class="location-badge">
              <span>{{ idx + 1 }}</span>
            </div>
            <span>{{ location.name }}</span>
          </div>
          <div class="location-meta">
            <div class="meta-item">
              <img :src="icons.distance" alt="" class="meta-icon" />
              <span>{{ location.distance }} km</span>
            </div>
            <div class="meta-item">
              <img :src="icons.money" alt="" class="meta-icon" />
              <span>{{ location.priceRange }}</span>
            </div>
          </div>
        </div>
        <div class="card-arrow">
          <svg viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6" stroke="currentColor" stroke-width="2" fill="none" />
          </svg>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import distanceIcon from '../assets/icons/Distance.svg'
import moneyIcon from '../assets/icons/Money.svg'

export type DesktopLocation = {
  id: number
  name: string
  distance: number
  priceRange: string
}

const props = defineProps<{
  locations: DesktopLocation[]
  userLocation?: { lat: number; lng: number } | null
}>()

const emit = defineEmits<{
  (event: 'location-click', locationId: number): void
}>()

const { t } = useI18n()

const searchTerm = ref('')

const filteredLocations = computed(() => {
  if (!searchTerm.value) return props.locations
  const query = searchTerm.value.toLowerCase()
  return props.locations.filter((loc) => loc.name.toLowerCase().includes(query))
})

const icons = {
  distance: distanceIcon,
  money: moneyIcon,
}
</script>

<style scoped>
.sidebar {
  width: 427px;
  background: #fafafc;
  border-radius: 0 32px 32px 0;
  box-shadow: 8px 0px 8px rgba(136, 136, 136, 0.16);
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 32px 0;
  margin-left: 0;
  overflow: hidden;
  z-index: 2;
  order: 1;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 16px;
  padding: 8px;
  background: #f2f4f6;
  border: 1px solid #dae2e3;
  border-radius: 8px;
  box-shadow: 0px 2px 2px rgba(136, 136, 153, 0.08);
}

.search-icon {
  width: 20px;
  height: 20px;
  color: #78898c;
  flex-shrink: 0;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  color: #414d4f;
  outline: none;
}

.search-input::placeholder {
  color: #78898c;
}

.divider {
  height: 16px;
}

.locations-header {
  display: flex;
  gap: 10px;
  padding: 0 16px;
  align-items: center;
}

.locations-header h2 {
  flex: 1;
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #414d4f;
  font-family: 'Inter', sans-serif;
}

.count {
  font-size: 12px;
  color: #78898c;
  font-family: 'Inter', sans-serif;
  font-weight: 400;
}

.locations-list {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 0 16px;
  overflow-y: auto;
}

.location-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  width: 100%;
  box-sizing: border-box;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.location-card:hover {
  background: #f3f4f6;
}

.card-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 0;
}

.location-name {
  display: flex;
  align-items: center;
  gap: 10px;
}

.location-badge {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #0b0b0b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 700;
  font-family: 'Inter', sans-serif;
  flex-shrink: 0;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
}

.location-badge span {
  line-height: 1;
}

.location-name > span {
  font-size: 16px;
  font-weight: 700;
  color: #242b2c;
  font-family: 'Inter', sans-serif;
  letter-spacing: 0.3px;
}

.location-meta {
  display: flex;
  gap: 16px;
  align-items: center;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #6b7280;
  font-family: 'Inter', sans-serif;
}

.meta-icon {
  width: 20px;
  height: 20px;
  color: #78898c;
  flex-shrink: 0;
}

.card-arrow {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  flex-shrink: 0;
}

.card-arrow svg {
  width: 100%;
  height: 100%;
  stroke-width: 2;
}

.locations-list::-webkit-scrollbar {
  width: 4px;
}

.locations-list::-webkit-scrollbar-track {
  background: transparent;
}

.locations-list::-webkit-scrollbar-thumb {
  background: #c2d0d2;
  border-radius: 2px;
}

.locations-list::-webkit-scrollbar-thumb:hover {
  background: #a8b8bc;
}
</style>
