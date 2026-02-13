<template>
  <button class="card" @click="handleClick" type="button">
    <div class="badge">{{ index }}</div>
    <div class="card-body">
      <h3>{{ location.name }}</h3>
      <div class="meta">
        <span>{{ location.distance }} km</span>
        <span>{{ location.priceRange }}</span>
      </div>
    </div>
  </button>
</template>

<script setup lang="ts">
import iconDistance from '../assets/icons/Distance.svg'
import iconMoney from '../assets/icons/Money.svg'

export type LocationItem = {
  id: number
  name: string
  distance: number
  priceRange: string
  lat: number
  lng: number
}

const props = defineProps<{ location: LocationItem; index: number }>()
const emit = defineEmits<{
  click: []
}>()

const handleClick = () => {
  emit('click')
}
</script>

<style scoped>
.card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 12px;
  background: #ffffff;
  border-radius: 16px;
  padding: 14px 16px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
  min-height: 72px;
  width: 100%;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
  font-size: inherit;
  text-align: left;
}

.card:hover {
  border-color: #005f6f;
  box-shadow: 0 8px 24px rgba(0, 95, 111, 0.12);
}

.card:active {
  border-color: #005f6f;
}

.badge {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  color: #0b0b0b;
  display: grid;
  place-items: center;
  font-weight: 700;
  font-size: 16px;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
  flex-shrink: 0;
}

.card-body h3 {
  margin: 0;
  font-size: 16px;
}

.meta {
  margin-top: 6px;
  display: flex;
  gap: 16px;
  color: #6b7280;
  font-size: 13px;
}

.meta span {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.meta img {
  width: 14px;
  height: 14px;
}

.chevron {
  width: 18px;
  height: 18px;
  opacity: 0.6;
}
</style>
