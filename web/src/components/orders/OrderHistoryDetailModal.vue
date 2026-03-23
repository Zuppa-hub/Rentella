<template>
  <div
    class="order-detail-modal-overlay"
    :class="{ desktop: isDesktop }"
    role="dialog"
    aria-modal="true"
    :aria-label="t('desktop.orders.orderDetails')"
    @click="emit('close')"
  >
    <div class="order-detail-modal" :class="{ desktop: isDesktop }" @click.stop>
      <button
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
          <span class="order-detail-label">{{ t('desktop.orders.orderId') }}:</span>
          <span class="order-detail-value">#{{ order.id }}</span>
        </div>
        <div class="order-detail-row">
          <span class="order-detail-label">{{ t('desktop.orders.location') }}:</span>
          <span class="order-detail-value">{{ order.cityName }}</span>
        </div>
        <div class="order-detail-row">
          <span class="order-detail-label">{{ t('desktop.orders.section') }}:</span>
          <span class="order-detail-value">{{ order.zoneName }}</span>
        </div>
        <div class="order-detail-row">
          <span class="order-detail-label">{{ t('desktop.orders.checkIn') }}:</span>
          <span class="order-detail-value">{{ order.checkInFormatted }}</span>
        </div>
        <div class="order-detail-row">
          <span class="order-detail-label">{{ t('desktop.orders.checkOut') }}:</span>
          <span class="order-detail-value">{{ order.checkOutFormatted }}</span>
        </div>
        <div class="order-detail-row order-detail-row-total">
          <span class="order-detail-label">{{ t('desktop.orders.totalPrice') }}:</span>
          <span class="order-detail-value">{{ order.totalPrice }}</span>
        </div>
      </div>
      <button class="order-detail-close-btn rt-btn rt-btn-primary" @click="emit('close')">
        {{ t('common.close') }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { HistoryOrderDetailView } from '../../types/orders'

defineProps<{
  order: HistoryOrderDetailView
  isDesktop?: boolean
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
  z-index: 2000;
  padding: 16px;
}

.order-detail-modal-overlay.desktop {
  align-items: center;
}

.order-detail-modal {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  width: 100%;
  max-width: 560px;
  position: relative;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  max-height: 100%;
  overflow-y: auto;
}

.order-detail-modal.desktop {
  border-radius: 20px;
  max-width: 640px;
  width: min(640px, calc(100vw - 64px));
  max-height: calc(100vh - 120px);
  overflow-y: auto;
  padding: 22px 24px 20px;
}

.order-detail-close {
  position: absolute;
  top: 16px;
  right: 16px;
  background: #f3f4f6;
  border: none;
  width: 34px;
  height: 34px;
  border-radius: 999px;
  padding: 0;
  cursor: pointer;
  color: #414d4f;
  display: flex;
  align-items: center;
  justify-content: center;
}

.order-detail-close:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
}

.order-detail-title {
  margin: 0 42px 16px 0;
  font-size: 20px;
  font-weight: 700;
  color: #242b2c;
}

.order-detail-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px 0;
  border-top: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.order-detail-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.order-detail-value {
  font-size: 13px;
  font-weight: 700;
  color: #242b2c;
  text-align: right;
}

.order-detail-row-total {
  padding: 10px 0 0;
  margin-top: 4px;
  border-top: 1px dashed #e5e7eb;
}

.order-detail-close-btn {
  width: 100%;
  margin-top: 16px;
}
</style>
