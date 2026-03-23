<template>
  <div class="active-orders-mobile-content">
    <div class="active-orders-header">
      <button class="active-orders-back" @click="emit('back')">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <h1 class="active-orders-title">{{ t('desktop.orders.activeTitle') }}</h1>
    </div>

    <p class="active-orders-subtitle">{{ t('desktop.orders.activeSubtitle') }}</p>

    <div v-if="loading" class="active-orders-loading" aria-live="polite">
      <p>{{ t('desktop.orders.loadingOrders') }}</p>
    </div>

    <div v-else-if="error" class="active-orders-error" role="alert">
      <p>{{ error }}</p>
      <button @click="emit('retry')" class="rt-btn rt-btn-primary">{{ t('desktop.orders.retry') }}</button>
    </div>

    <div v-else-if="activeOrdersView.length === 0" class="active-orders-empty">
      <p>{{ t('desktop.orders.noActiveOrders') }}</p>
    </div>

    <div v-else class="active-orders-list" role="list" :aria-label="t('desktop.orders.activeOrdersListAria')">
      <div
        v-for="order in activeOrdersView"
        :key="order.id"
        class="active-order-card"
        @click="emit('select', order.raw)"
        @keydown.enter.prevent="emit('select', order.raw)"
        @keydown.space.prevent="emit('select', order.raw)"
        :aria-label="t('desktop.orders.openActiveOrderDetails', { id: order.id })"
        role="listitem"
        tabindex="0"
      >
        <div class="active-order-main">
          <h3>{{ order.beachName }}</h3>
          <span>{{ order.cityName }}</span>
          <small>{{ order.zoneName }} • {{ order.price }} • {{ order.startDateFormatted }}</small>
        </div>
        <button
          type="button"
          class="mobile-action-btn"
          :class="{ cancel: order.isCancellable, active: !order.isCancellable }"
          :disabled="!order.isCancellable || loadingActionId === order.id"
          @click.stop="emit('cancel', order.id)"
        >
          {{
            loadingActionId === order.id
              ? t('desktop.orders.cancelling')
              : order.isCancellable
                ? t('desktop.orders.cancel')
                : t('desktop.orders.activeStatus')
          }}
        </button>
      </div>
    </div>

    <p v-if="actionError" class="desktop-action-error" role="alert">{{ actionError }}</p>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { Order } from '../../services/api'
import type { ActiveOrderView } from '../../types/orders'

defineProps<{
  loading: boolean
  error: string | null
  actionError: string | null
  loadingActionId: number | null
  activeOrdersView: ActiveOrderView[]
}>()

const emit = defineEmits<{
  back: []
  retry: []
  select: [order: Order]
  cancel: [orderId: number]
}>()

const { t } = useI18n()
</script>

<style scoped>
.active-orders-mobile-content {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;
}

.active-orders-header {
  display: flex;
  align-items: center;
  gap: 12px;
  position: sticky;
  top: 0;
  z-index: 2;
  background: #ffffff;
  padding-bottom: 12px;
  margin-bottom: 8px;
}

.active-orders-back {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #414d4f;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.active-orders-back:hover {
  color: var(--color-primary, #005f6f);
}

.active-orders-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
}

.active-orders-subtitle {
  margin: 0 0 20px;
  font-size: 14px;
  color: #78898c;
  font-weight: 400;
}

.active-orders-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-bottom: 8px;
}

.active-order-card {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 12px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.active-order-card:hover {
  border-color: #d7dee3;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
}

.active-order-card:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
}

.active-order-main {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.active-order-main h3 {
  margin: 0;
  font-size: 14px;
  color: #242b2c;
}
.active-order-main span {
  font-size: 12px;
  color: #73858a;
}

.active-order-main small {
  font-size: 11px;
  color: #6d7b80;
}

.mobile-action-btn {
  border: 0;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
}
.mobile-action-btn.active {
  background: #7a8d91;
}
.mobile-action-btn.cancel {
  background: var(--color-primary, #005f6f);
}

.active-orders-loading,
.active-orders-error,
.active-orders-empty {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 12px;
}

.desktop-action-error {
  margin: 6px 0 0;
  font-size: 12px;
  color: #b42318;
  text-align: center;
}
</style>
