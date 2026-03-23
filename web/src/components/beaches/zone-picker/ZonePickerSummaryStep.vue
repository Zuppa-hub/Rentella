<template>
  <div class="zone-checkout-summary">
    <h3 class="zone-checkout-title">{{ t('desktop.zonePicker.checkout') }}</h3>
    <div class="zone-checkout-table">
      <div class="zone-checkout-row">
        <span class="zone-checkout-label">{{ t('desktop.zonePicker.location') }}</span>
        <span class="zone-checkout-value">{{ locationName }}</span>
      </div>
      <div class="zone-checkout-row">
        <span class="zone-checkout-label">{{ t('desktop.zonePicker.beach') }}</span>
        <span class="zone-checkout-value">{{ selectedZoneBeach?.name }}</span>
      </div>
      <div class="zone-checkout-row">
        <span class="zone-checkout-label">{{ t('desktop.zonePicker.section') }}</span>
        <span class="zone-checkout-value">{{ selectedZone?.name }}</span>
      </div>
      <div class="zone-checkout-row">
        <span class="zone-checkout-label">{{ t('desktop.zonePicker.price') }}</span>
        <span class="zone-checkout-value">{{ zoneDailyPrice }}</span>
      </div>
      <div class="zone-checkout-row">
        <span class="zone-checkout-label">{{ t('desktop.zonePicker.duration') }}</span>
        <span class="zone-checkout-value">{{ formattedDuration }}</span>
      </div>
    </div>

    <div class="zone-checkout-total">
      <span>{{ t('desktop.zonePicker.total') }}</span>
      <span>{{ selectedZonePrice }}</span>
    </div>

    <p class="zone-picker-note rt-text-muted">{{ t('desktop.zonePicker.note') }}</p>
    <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
      {{ checkoutFeedback.message }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { BeachViewModel, BeachZoneViewModel } from '../../../types/beaches'

defineProps<{
  locationName: string
  selectedZoneBeach: BeachViewModel | null
  selectedZone: BeachZoneViewModel | null
  zoneDailyPrice: string
  formattedDuration: string
  selectedZonePrice: string
  checkoutFeedback: { type: 'success' | 'error' | 'info'; message: string } | null
}>()

const { t } = useI18n()
</script>

<style scoped>
.zone-checkout-summary {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.zone-checkout-title {
  margin: 0;
  font-size: 20px;
  color: #242b2c;
  font-weight: 700;
}

.zone-checkout-table {
  border-top: 1px solid #c6d0d3;
  border-bottom: 1px solid #c6d0d3;
  padding: 12px 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.zone-checkout-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: center;
}

.zone-checkout-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.zone-checkout-value {
  font-size: 13px;
  font-weight: 700;
  color: #3f4a4f;
  text-align: right;
}

.zone-checkout-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 20px;
  line-height: 1.2;
  font-weight: 700;
  color: #3f4a4f;
}

.zone-picker-note {
  margin: 10px 0 0;
  color: #73858a;
  line-height: 1.35;
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
