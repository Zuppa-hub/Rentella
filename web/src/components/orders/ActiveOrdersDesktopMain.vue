<template>
  <section class="desktop-main" :aria-label="t('desktop.orders.activeTitle')">
    <h2 class="desktop-main-title">{{ t('desktop.orders.activeTitle') }}</h2>

    <div v-if="loading" class="active-orders-loading" aria-live="polite">
      <SkeletonLoader
        :rows="4"
        :height="18"
        :gap="12"
        :widths="['96%', '100%', '92%', '98%']"
        :aria-label="t('desktop.orders.loadingOrders')"
      />
    </div>

    <div v-else-if="error" class="active-orders-error" role="alert">
      <p>{{ error }}</p>
      <button @click="emit('retry')" class="rt-btn rt-btn-primary">{{ t('desktop.orders.retry') }}</button>
    </div>

    <div v-else-if="activeOrdersView.length === 0" class="active-orders-empty">
      <p>{{ t('desktop.orders.noActiveOrders') }}</p>
    </div>

    <div
      v-else
      class="desktop-orders-list"
      role="list"
      :aria-label="t('desktop.orders.activeOrdersListAria')"
    >
      <button
        v-for="order in activeOrdersView"
        :key="order.id"
        type="button"
        class="desktop-order-row"
        @click="emit('select', order.raw)"
        :aria-label="t('desktop.orders.openActiveOrderDetails', { id: order.id })"
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
        <div class="desktop-order-action">
          <button
            type="button"
            class="desktop-action-btn"
            :class="{ cancel: order.isCancellable, active: !order.isCancellable }"
            :disabled="!order.isCancellable || loadingActionId === order.id"
            @click.stop="emit('cancel', order.id)"
          >
            {{
              loadingActionId === order.id
                ? t('desktop.orders.cancelling')
                : order.isCancellable
                  ? t('desktop.orders.cancelOrder')
                  : t('desktop.orders.activeStatus')
            }}
          </button>
        </div>
      </button>

      <p v-if="actionError" class="desktop-action-error" role="alert">{{ actionError }}</p>
      <p class="desktop-records-count">
        {{ t('desktop.orders.recordsShown', { count: activeOrdersView.length }) }}
      </p>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { Order } from '../../services/api'
import type { ActiveOrderView } from '../../types/orders'
import SkeletonLoader from '../common/SkeletonLoader.vue'

defineProps<{
  loading: boolean
  error: string | null
  actionError: string | null
  loadingActionId: number | null
  activeOrdersView: ActiveOrderView[]
}>()

const emit = defineEmits<{
  retry: []
  select: [order: Order]
  cancel: [orderId: number]
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
  font-weight: 600;
  color: var(--color-text, #414d4f);
}

.desktop-orders-list {
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.desktop-order-row {
  border: 1px solid var(--color-border, #e2e8ec);
  border-radius: 16px;
  background: #fff;
  padding: 12px 14px;
  display: grid;
  grid-template-columns: 2.8fr 1.1fr 1.1fr 1fr 1fr 1fr 1.6fr;
  gap: 8px;
  text-align: left;
  cursor: pointer;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
}

.desktop-order-col {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.desktop-order-label {
  font-size: 11px;
  color: #73858a;
  font-weight: 600;
}
.desktop-order-col strong {
  font-size: 15px;
  color: var(--color-text, #414d4f);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-order-action {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.desktop-action-btn {
  border: 0;
  border-radius: 8px;
  padding: 10px 14px;
  min-width: 122px;
  font-size: 13px;
  font-weight: 700;
  color: #fff;
}

.desktop-action-btn.active {
  background: #7a8d91;
  cursor: default;
}

.desktop-action-btn.cancel {
  background: var(--color-primary, #005f6f);
  cursor: pointer;
}

.desktop-action-btn:disabled {
  opacity: 0.95;
}

.desktop-records-count {
  margin: 12px 0 0;
  text-align: center;
  font-size: 13px;
  color: #6f7c81;
}

.desktop-action-error {
  margin: 6px 0 0;
  font-size: 12px;
  color: #b42318;
  text-align: center;
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
</style>
