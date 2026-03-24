<template>
  <aside class="desktop-sidebar">
    <h1 class="desktop-sidebar-title">{{ t('desktop.orders.historyTitle') }}</h1>

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

    <div class="desktop-active-link-wrap">
      <p>{{ t('desktop.orders.activeOrdersHint') }}</p>
      <button type="button" class="desktop-active-link" @click="emit('navigate', 'active')">
        {{ t('desktop.nav.active') }}
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
}>()

const emit = defineEmits<{
  navigate: [tab: string]
}>()

const { t } = useI18n()
</script>

<style scoped>
.desktop-sidebar {
  border-right: 1px solid var(--color-border, #c9d1d4);
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
  line-height: 1.2;
  font-weight: 600;
  color: var(--color-text, #414d4f);
}

.desktop-sidebar-title::after {
  content: '';
  display: block;
  margin-top: 16px;
  height: 1px;
  background: var(--color-border, #c9d1d4);
}

.desktop-metrics {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.desktop-metric-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  color: #73858a;
  font-size: 12px;
  line-height: 1.2;
}

.desktop-metric-row strong {
  color: var(--color-text, #414d4f);
  font-size: 13px;
  font-weight: 700;
}

.desktop-active-link-wrap {
  margin-top: auto;
  padding-top: 28px;
  text-align: center;
  color: #73858a;
  font-size: 12px;
}

.desktop-active-link-wrap p {
  margin: 0 0 8px;
}

.desktop-active-link {
  border: 0;
  background: transparent;
  font-size: 14px;
  line-height: 1.2;
  font-weight: 700;
  color: var(--color-text, #414d4f);
  cursor: pointer;
}

.desktop-active-link:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
}
</style>
