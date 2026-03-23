<template>
  <section class="desktop-main" :aria-label="t('desktop.orders.previousPurchases')">
    <h2 class="desktop-main-title">{{ t('desktop.orders.previousPurchases') }}</h2>

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

    <div v-else class="desktop-orders-list" role="list" :aria-label="t('desktop.orders.ordersListAria')">
      <button
        v-for="order in finishedOrdersView"
        :key="order.id"
        type="button"
        class="desktop-order-row"
        @click="emit('select', order.raw)"
        :aria-label="t('desktop.orders.openOrderDetails', { id: order.id })"
        role="listitem"
      >
        <div class="desktop-order-col desktop-order-col-beach">
          <span class="desktop-order-label">{{ t('desktop.orders.beachName') }}</span>
          <strong>{{ order.beachName }}</strong>
        </div>
        <div class="desktop-order-col">
          <span class="desktop-order-label">{{ t('desktop.orders.orderId') }}</span>
          <strong>#{{ order.id }}</strong>
        </div>
        <div class="desktop-order-col">
          <span class="desktop-order-label">{{ t('desktop.orders.section') }}</span>
          <strong>{{ order.zoneName }}</strong>
        </div>
        <div class="desktop-order-col">
          <span class="desktop-order-label">{{ t('desktop.orders.umbrella') }}</span>
          <strong>{{ order.umbrellaNumber }}</strong>
        </div>
        <div class="desktop-order-col">
          <span class="desktop-order-label">{{ t('desktop.orders.price') }}</span>
          <strong>{{ order.price }}</strong>
        </div>
        <div class="desktop-order-col">
          <span class="desktop-order-label">{{ t('desktop.orders.date') }}</span>
          <strong>{{ order.startDateFormatted }}</strong>
        </div>
      </button>

      <p class="desktop-records-count">{{ t('desktop.orders.recordsShown', { count: finishedOrdersView.length }) }}</p>
    </div>
  </section>
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
  retry: []
  select: [order: Order]
}>()

const { t } = useI18n()
</script>

<style scoped>
.desktop-main {
  padding: 24px 24px 18px;
  display: flex;
  flex-direction: column;
  min-width: 0;
  min-height: 0;
  overflow: hidden;
}

.desktop-main-title {
  margin: 0 0 18px;
  font-size: 20px;
  line-height: 1.2;
  color: var(--color-text, #414d4f);
  font-weight: 600;
}

.desktop-orders-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-right: 4px;
}

.desktop-order-row {
  border: 1px solid var(--color-border, #e2e8ec);
  border-radius: 16px;
  background: #ffffff;
  display: grid;
  grid-template-columns: 2.8fr 1.2fr 1.2fr 1.1fr 1fr 1fr;
  gap: 10px;
  padding: 14px 16px;
  text-align: left;
  cursor: pointer;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
  transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.desktop-order-row:hover {
  background: #f8fafb;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.09);
  transform: translateY(-1px);
}

.desktop-order-row:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
  box-shadow: 0 0 0 3px rgba(0, 95, 111, 0.18);
}

.desktop-order-col {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.desktop-order-col-beach strong {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-order-label {
  color: #73858a;
  font-size: 12px;
  font-weight: 600;
}

.desktop-order-col strong {
  color: var(--color-text, #414d4f);
  font-size: 16px;
  line-height: 1.2;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-records-count {
  text-align: center;
  margin: 16px 0 0;
  color: #6b7280;
  font-size: 13px;
  font-weight: 600;
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
</style>
