<template>
  <article class="card">
    <div class="badge">{{ index }}</div>
    <div class="card-body">
      <h3>{{ location.name }}</h3>
      <div class="meta">
        <span>
          <img :src="icons.distance" alt="" />
          {{ location.distance }} km
        </span>
        <span>
          <img :src="icons.money" alt="" />
          {{ location.priceRange }}
        </span>
      </div>
    </div>
    <img :src="icons.arrow" alt="" class="chevron" />
  </article>
</template>

<script setup lang="ts">
import iconArrow from '../assets/icons/Arrow.svg'
import iconDistance from '../assets/icons/Distance.svg'
import iconMoney from '../assets/icons/Money.svg'

export type LocationItem = {
  id: number
  name: string
  distance: number
  priceRange: string
}

defineProps<{ location: LocationItem; index: number }>()

const icons = {
  arrow: iconArrow,
  distance: iconDistance,
  money: iconMoney,
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
}

.badge {
  width: 32px;
  height: 32px;
  border-radius: 999px 999px 999px 6px;
  background: #1f2937;
  color: #ffffff;
  display: grid;
  place-items: center;
  font-weight: 600;
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
