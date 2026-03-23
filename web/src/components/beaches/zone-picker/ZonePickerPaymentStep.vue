<template>
  <div class="zone-payment-form">
    <div class="zone-payment-header">
      <h3 class="zone-payment-title">{{ t('desktop.zonePicker.payment') }}</h3>
      <span class="zone-payment-price">{{ selectedZonePrice }}</span>
    </div>

    <div class="zone-payment-divider"></div>

    <div class="zone-payment-demo-notice" role="note">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="12" y1="8" x2="12" y2="12"></line>
        <line x1="12" y1="16" x2="12.01" y2="16"></line>
      </svg>
      <span>{{ t('desktop.zonePicker.demoModeNotice') }}</span>
    </div>

    <div class="zone-payment-mock-card" aria-hidden="true">
      <span class="zone-payment-mock-card-number">•••• •••• •••• 0000</span>
      <div class="zone-payment-mock-card-row">
        <span class="zone-payment-mock-card-name">{{ t('desktop.zonePicker.demoCardholder') }}</span>
        <span class="zone-payment-mock-card-expiry">12/9999</span>
      </div>
    </div>

    <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
      {{ checkoutFeedback.message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  selectedZonePrice: string
  checkoutFeedback: { type: 'success' | 'error' | 'info'; message: string } | null
}>()

const { t } = useI18n()
</script>

<style scoped>
.zone-payment-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.zone-payment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.zone-payment-title,
.zone-payment-price {
  margin: 0;
  font-size: 28px;
  color: #242b2c;
  font-weight: 700;
}

.zone-payment-divider {
  height: 1px;
  background: #c6d0d3;
}

.zone-payment-demo-notice {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 12px 14px;
  background: #fff7ed;
  border: 1px solid #fed7aa;
  border-radius: 10px;
  font-size: 13px;
  color: #92400e;
  line-height: 1.45;
}

.zone-payment-demo-notice svg {
  flex-shrink: 0;
  margin-top: 1px;
}

.zone-payment-mock-card {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 20px 24px;
  background: linear-gradient(135deg, #1e3a5f 0%, #005f6f 100%);
  border-radius: 14px;
  color: #ffffff;
  font-family: monospace;
  user-select: none;
  opacity: 0.85;
}

.zone-payment-mock-card-number {
  font-size: 18px;
  letter-spacing: 2px;
}

.zone-payment-mock-card-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  opacity: 0.85;
}

.zone-picker-feedback {
  margin: 8px 0 0;
  font-size: 13px;
  line-height: 1.35;
  font-weight: 600;
}

.zone-picker-feedback.info {
  color: #0b4b58;
}

.zone-picker-feedback.success {
  color: #027a48;
}

.zone-picker-feedback.error {
  color: #b42318;
}
</style>
