<template>
  <div
    class="order-detail-modal-overlay"
    role="dialog"
    aria-modal="true"
    :aria-label="t('desktop.orders.orderDetails')"
    @click="emit('close')"
  >
    <div class="order-detail-modal" @click.stop>
      <button
        type="button"
        class="order-detail-close"
        @click="emit('close')"
        :aria-label="t('desktop.orders.closeOrderDetails')"
      >
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h3 class="order-detail-title">{{ order.beachName }}</h3>
      <div class="order-detail-content">
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.orderId') }}:</span><strong>#{{ order.id }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.location') }}:</span><strong>{{ order.cityName }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.section') }}:</span><strong>{{ order.zoneName }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.umbrella') }}:</span><strong>{{ order.umbrellaNumber }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.checkIn') }}:</span><strong>{{ order.checkInFormatted }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.checkOut') }}:</span><strong>{{ order.checkOutFormatted }}</strong>
        </div>
        <div class="order-detail-row">
          <span>{{ t('desktop.orders.totalPrice') }}:</span><strong>{{ order.totalPrice }}</strong>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { OrderDetailView } from '../../types/orders'

defineProps<{
  order: OrderDetailView
}>()

const emit = defineEmits<{
  close: []
}>()

const { t } = useI18n()
</script>

<style scoped>
.order-detail-modal-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 2000;
}

.order-detail-modal {
  width: min(560px, 100%);
  max-height: 100%;
  overflow: auto;
  background: #fff;
  border-radius: 18px;
  padding: 20px;
  position: relative;
}

.order-detail-close {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 32px;
  height: 32px;
  border: 0;
  border-radius: 999px;
  background: #f3f4f6;
  color: #414d4f;
  display: grid;
  place-items: center;
}

.order-detail-title {
  margin: 0 36px 14px 0;
  font-size: 18px;
  color: #242b2c;
}

.order-detail-content {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 13px;
  color: #5b6b70;
}

.order-detail-row strong {
  color: #2a3436;
}
</style>
