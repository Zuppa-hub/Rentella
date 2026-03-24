<template>
  <div class="order-history-mobile-content">
    <div class="order-history-header">
      <button class="order-history-back" type="button" :aria-label="t('common.back')" @click="emit('back')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <h1 class="order-history-title">{{ t('desktop.orders.historyTitle') }}</h1>
    </div>

    <p class="order-history-subtitle">{{ t('desktop.orders.historySubtitle') }}</p>

    <div v-if="loading" class="order-history-loading" aria-live="polite">
      <p>{{ t('desktop.orders.loadingOrders') }}</p>
    </div>

    <div v-else-if="error" class="order-history-error" role="alert">
      <p>{{ error }}</p>
      <button @click="emit('retry')" class="rt-btn rt-btn-primary">{{ t('desktop.orders.retry') }}</button>
    </div>

    <div v-else-if="finishedOrdersView.length === 0" class="order-history-empty">
      <p>{{ t('desktop.orders.noOrdersFound') }}</p>
    </div>

    <div v-else class="order-history-list" role="list" :aria-label="t('desktop.orders.ordersListAria')">
      <button
        v-for="order in finishedOrdersView"
        :key="order.id"
        class="order-card"
        type="button"
        @click="emit('select', order.raw)"
        :aria-label="t('desktop.orders.openOrderDetails', { id: order.id })"
        role="listitem"
      >
        <div class="order-card-content">
          <div class="order-card-main">
            <h3 class="order-card-beach">{{ order.beachName }}</h3>
            <span class="order-card-city">{{ order.cityName }}</span>
          </div>
          <span class="order-card-date">{{ order.startDateFormatted }}</span>
        </div>
        <svg
          class="order-card-arrow"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
    </div>

    <p v-if="!loading && finishedOrdersView.length > 0" class="order-history-count">
      {{ t('desktop.orders.records', { count: finishedOrdersView.length }) }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { Order } from '../../services/api'
import type { HistoryOrderView } from '../../types/orders'

defineProps<{
  loading: boolean
  error: string | null
  finishedOrdersView: HistoryOrderView[]
}>()

const emit = defineEmits<{
  back: []
  retry: []
  select: [order: Order]
}>()

const { t } = useI18n()
</script>

<style scoped>
.order-history-mobile-content {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;
}

.order-history-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 12px;
  margin-bottom: 8px;
}

.order-history-back {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #414d4f;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.order-history-back:hover {
  color: var(--color-primary, #005f6f);
}

.order-history-back:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
}

.order-history-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
  flex: 1;
}

.order-history-subtitle {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: #78898c;
  font-weight: 400;
}

.order-history-loading,
.order-history-error,
.order-history-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
}

.order-history-error p {
  margin: 0;
  color: #d32f2f;
  font-size: 14px;
}

.order-history-empty p {
  margin: 0;
  color: #78898c;
  font-size: 16px;
}

.order-history-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0;
}

.order-card {
  border: 0;
  text-align: left;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
  transition:
    background-color 0.2s ease,
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.order-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.09);
}

.order-card:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
}

.order-card-content {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-width: 0;
}

.order-card-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.order-card-beach {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  color: #242b2c;
}

.order-card-city {
  font-size: 12px;
  color: #78898c;
}

.order-card-date {
  font-size: 13px;
  color: #414d4f;
  font-weight: 600;
  white-space: nowrap;
}

.order-card-arrow {
  color: #8a969b;
  flex-shrink: 0;
}

.order-history-count {
  margin: 20px 0 16px 0;
  font-size: 13px;
  color: #9ca3af;
  text-align: center;
  font-weight: 600;
}
</style>
