<template>
  <section class="sheet" :class="{ collapsed: isCollapsed }">
    <div class="sheet-header">
      <button class="sheet-toggle" @click="toggleCollapsed">
        {{ isCollapsed ? 'Show' : 'Hide' }}
        <img :src="icons.arrowDown" alt="" :class="{ rotated: isCollapsed }" />
      </button>
    </div>
    <div class="search" @click="isCollapsed && toggleCollapsed()">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none" />
        <path d="M20 20l-4-4" stroke="currentColor" stroke-width="2" fill="none" />
      </svg>
      <input
        :value="searchTerm"
        placeholder="Search for a location..."
        @input="onSearch"
      />
    </div>

    <div v-show="!isCollapsed" class="list-header">
      <h2>Available locations</h2>
      <span>{{ totalCount }} Locations</span>
    </div>

    <div v-show="!isCollapsed" class="location-list">
      <LocationCard
        v-for="(location, index) in locations"
        :key="location.id"
        :location="location"
        :index="index + 1"
      />
    </div>
  </section>
</template>

<script setup lang="ts">
import iconArrowDown from '../assets/icons/ArrowDown.svg'
import LocationCard, { type LocationItem } from './LocationCard.vue'

const props = defineProps<{
  locations: LocationItem[]
  totalCount: number
  searchTerm: string
  isCollapsed: boolean
}>()

const emit = defineEmits<{
  (event: 'update:searchTerm', value: string): void
  (event: 'update:isCollapsed', value: boolean): void
}>()

const toggleCollapsed = () => {
  emit('update:isCollapsed', !props.isCollapsed)
}

const icons = {
  arrowDown: iconArrowDown,
}

const onSearch = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:searchTerm', target.value)
}
</script>

<style scoped>
.sheet {
  flex: 1;
  margin-top: -67px;
  background: #ffffff;
  border-radius: 32px 32px 0 0;
  padding: 20px clamp(12px, 4vw, 32px) calc(100px + env(safe-area-inset-bottom));
  box-shadow: 0 -2px 8px rgba(85, 85, 102, 0.18);
  min-height: calc(100vh - 80px - (64px + 204px - 67px));
  max-height: calc(100vh - 80px - (64px + 204px - 67px));
  overflow-y: auto;
  box-sizing: border-box;
  position: relative;
  z-index: 100;
  transition: all 0.4s ease-in-out;
}

.sheet.collapsed {
  flex: 0 0 auto;
  min-height: none;
  max-height: none;
  padding: 20px clamp(12px, 4vw, 32px) 60px;
  border-radius: 32px 32px 0 0;
  margin-top: -150px;
  overflow: visible;
  background: #ffffff;
  box-shadow: 0 -2px 8px rgba(85, 85, 102, 0.18);
  position: relative;
}

.sheet.collapsed .location-list,
.sheet.collapsed .list-header {
  display: none;
}

.sheet-header {
  display: flex;
  justify-content: center;
  margin-bottom: 16px;
}

.sheet-toggle {
  border: none;
  background: transparent;
  color: #374151;
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
  font-size: 15px;
  cursor: pointer;
}

.sheet-toggle img {
  width: 16px;
  height: 16px;
  transition: transform 0.3s ease;
}

.sheet-toggle img.rotated {
  transform: rotate(180deg);
}

.search {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f3f4f6;
  border-radius: 12px;
  padding: 12px 14px;
  margin-bottom: 20px;
  min-height: 48px;
}

.search svg {
  width: 18px;
  height: 18px;
  color: #9ca3af;
  flex-shrink: 0;
}

.search input {
  border: none;
  background: transparent;
  width: 100%;
  font-size: 16px;
  font-family: inherit;
  outline: none;
  color: #111827;
}

.search input::placeholder {
  color: #9ca3af;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.list-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #111827;
}

.list-header span {
  color: #6b7280;
  font-size: 14px;
  font-weight: 500;
}

.location-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  max-height: calc(100vh - 140px - (64px + 204px - 67px) - 220px);
  overflow-y: auto;
  padding-right: 4px;
}
</style>
