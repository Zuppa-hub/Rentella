<template>
  <div class="zone-success-page">
    <div class="zone-success-header">
      <svg class="zone-success-icon" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="20 6 9 17 4 12"></polyline>
      </svg>
      <h3 class="zone-success-title">{{ t('desktop.zonePicker.reservationConfirmed') }}</h3>
    </div>

    <div class="zone-success-order-id">
      <span class="zone-success-label">{{ t('desktop.zonePicker.orderId') }}</span>
      <span class="zone-success-value">{{ orderId }}</span>
    </div>

    <div class="zone-success-divider"></div>

    <div class="zone-success-details">
      <div class="zone-success-row">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.location') }}</span>
        <span class="zone-success-detail-value">{{ locationName }}</span>
      </div>
      <div class="zone-success-row">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.beach') }}</span>
        <span class="zone-success-detail-value">{{ selectedZoneBeach?.name }}</span>
      </div>
      <div class="zone-success-row">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.section') }}</span>
        <span class="zone-success-detail-value">{{ selectedZone?.name }}</span>
      </div>
      <div class="zone-success-row">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.checkIn') }}</span>
        <span class="zone-success-detail-value">{{ reservationFrom }}</span>
      </div>
      <div class="zone-success-row">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.checkOut') }}</span>
        <span class="zone-success-detail-value">{{ reservationTo }}</span>
      </div>
      <div class="zone-success-row zone-success-row-total">
        <span class="zone-success-detail-label">{{ t('desktop.zonePicker.totalPrice') }}</span>
        <span class="zone-success-detail-value">{{ selectedZonePrice }}</span>
      </div>
    </div>

    <p class="zone-success-note">{{ t('desktop.zonePicker.successNote') }}</p>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { BeachViewModel, BeachZoneViewModel } from '../../../types/beaches'

defineProps<{
  orderId: string | null
  locationName: string
  selectedZoneBeach: BeachViewModel | null
  selectedZone: BeachZoneViewModel | null
  reservationFrom: string
  reservationTo: string
  selectedZonePrice: string
}>()

const { t } = useI18n()
</script>

<style scoped>
.zone-success-page {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.zone-success-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px 0;
}

.zone-success-icon {
  color: #059669;
  animation: scaleIn 0.4s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.zone-success-title {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #242b2c;
  text-align: center;
}

.zone-success-order-id {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 12px;
}

.zone-success-label {
  font-size: 12px;
  font-weight: 600;
  color: #73858a;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.zone-success-value {
  font-size: 18px;
  font-weight: 700;
  color: #059669;
  font-family: 'Courier New', monospace;
}

.zone-success-divider {
  height: 1px;
  background: #e5e7eb;
}

.zone-success-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.zone-success-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 0;
}

.zone-success-row-total {
  padding: 12px 0;
  border-top: 1px solid #e5e7eb;
  font-size: 16px;
  font-weight: 700;
}

.zone-success-detail-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.zone-success-detail-value {
  font-size: 13px;
  font-weight: 700;
  color: #242b2c;
  text-align: right;
}

.zone-success-note {
  margin: 8px 0 0;
  font-size: 12px;
  color: #73858a;
  line-height: 1.4;
  text-align: center;
  font-style: italic;
}
</style>
