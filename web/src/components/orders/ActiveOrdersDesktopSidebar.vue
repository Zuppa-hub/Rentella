<template>
  <aside class="desktop-sidebar">
    <h1 class="desktop-sidebar-title">{{ t('desktop.orders.activeTitle') }}</h1>

    <div class="desktop-metrics">
      <div class="desktop-metric-row">
        <span>{{ t('desktop.orders.totalOrders') }}</span>
        <strong>{{ totalOrders }}</strong>
      </div>
      <div class="desktop-metric-row">
        <span>{{ t('desktop.orders.favouriteCity') }}</span>
        <strong>{{ favouriteCity }}</strong>
      </div>
      <div class="desktop-metric-row">
        <span>{{ t('desktop.orders.favouriteBeach') }}</span>
        <strong>{{ favouriteBeach }}</strong>
      </div>
    </div>

    <div class="desktop-most-visited">
      <h2>{{ t('desktop.orders.mostVisitedBeaches') }}</h2>
      <div class="desktop-most-visited-list">
        <div v-for="(item, index) in topVisitedBeaches" :key="item.name" class="desktop-most-visited-item">
          <span class="desktop-most-visited-rank">{{ index + 1 }}.</span>
          <div class="desktop-most-visited-card">
            <div class="desktop-most-visited-thumb" aria-hidden="true">🏖️</div>
            <div class="desktop-most-visited-content">
              <strong>{{ item.name }}</strong>
              <span>{{ item.city }} • {{ t('desktop.orders.ordersCount', { count: item.count }) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="desktop-history-link-wrap">
      <p>{{ t('desktop.orders.previousOrdersHint') }}</p>
      <button type="button" class="desktop-history-link" @click="emit('navigate', 'history')">
        {{ t('desktop.nav.history') }}
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  totalOrders: number
  favouriteCity: string
  favouriteBeach: string
  topVisitedBeaches: Array<{ name: string; city: string; count: number }>
}>()

const emit = defineEmits<{
  navigate: [tab: string]
}>()

const { t } = useI18n()
</script>

<style scoped>
.desktop-sidebar {
  border-right: 1px solid var(--color-border, #e2e8ec);
  padding: 32px 24px;
  background: #f3f4f5;
  overflow-y: auto;
  min-height: 0;
  display: flex;
  flex-direction: column;
}

.desktop-sidebar-title {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: var(--color-text, #414d4f);
}

.desktop-sidebar-title::after {
  content: '';
  display: block;
  margin-top: 14px;
  height: 1px;
  background: var(--color-border, #e2e8ec);
}

.desktop-metrics {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.desktop-metric-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 12px;
  color: #73858a;
}

.desktop-metric-row strong {
  font-size: 13px;
  color: var(--color-text, #414d4f);
}

.desktop-most-visited {
  margin-top: 28px;
}

.desktop-most-visited h2 {
  margin: 0 0 12px;
  font-size: 14px;
  font-weight: 700;
  color: var(--color-text, #414d4f);
}

.desktop-most-visited-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.desktop-most-visited-item {
  display: grid;
  grid-template-columns: 22px 1fr;
  align-items: center;
  gap: 8px;
}

.desktop-most-visited-rank {
  font-size: 16px;
  font-weight: 700;
  color: #57676c;
}

.desktop-most-visited-card {
  border: 1px solid var(--color-border, #e2e8ec);
  border-radius: 14px;
  background: #ffffff;
  padding: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.desktop-most-visited-thumb {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  background: #f0f4f6;
  display: grid;
  place-items: center;
  font-size: 24px;
}

.desktop-most-visited-content {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.desktop-most-visited-content strong {
  font-size: 12px;
  color: #2a3436;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-most-visited-content span {
  font-size: 11px;
  color: #73858a;
}

.desktop-history-link-wrap {
  margin-top: auto;
  padding-top: 18px;
  text-align: center;
  font-size: 12px;
  color: #73858a;
}

.desktop-history-link {
  border: 0;
  background: transparent;
  color: var(--color-text, #414d4f);
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
}
</style>
