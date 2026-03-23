<template>
  <div class="zone-picker-menu">
    <div class="zone-picker-hero">
      <img v-if="selectedZoneBeach?.photo_url" :src="selectedZoneBeach.photo_url" :alt="selectedZoneBeach.name" class="zone-picker-hero-image" loading="lazy" />
      <div v-else class="zone-picker-hero-placeholder">🏖️</div>
    </div>

    <div class="zone-picker-content">
      <div v-if="zonePickerStep !== 'payment'" class="zone-picker-summary">
        <div class="zone-picker-title-wrap">
          <h3 class="zone-picker-beach-name rt-text-title-sm">{{ selectedZoneBeach?.name }}</h3>
          <span class="zone-picker-zone-name rt-text-muted">{{ selectedZone?.name }}</span>
        </div>
        <span class="zone-picker-price rt-text-title-sm">{{ selectedZonePrice }}</span>
      </div>

      <div v-if="zonePickerStep !== 'payment'" class="zone-picker-divider"></div>

      <div v-if="zonePickerStep === 'form'" class="zone-picker-form">
        <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.name') }}</label>
        <input
          :value="reservationName"
          type="text"
          class="zone-picker-input"
          :placeholder="t('desktop.zonePicker.namePlaceholder')"
          autocomplete="name"
          maxlength="80"
          @input="emit('update:reservationName', ($event.target as HTMLInputElement).value)"
        />

        <div class="zone-picker-dates-grid">
          <div>
            <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.from') }}</label>
            <div class="zone-picker-date-wrap">
              <input
                :value="reservationFrom"
                type="date"
                class="zone-picker-input zone-picker-date"
                :min="todayDate"
                @input="emit('update:reservationFrom', ($event.target as HTMLInputElement).value)"
              />
              <svg class="zone-picker-date-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
              </svg>
            </div>
          </div>

          <div>
            <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.to') }}</label>
            <div class="zone-picker-date-wrap">
              <input
                :value="reservationTo"
                type="date"
                class="zone-picker-input zone-picker-date"
                :min="reservationFrom || todayDate"
                @input="emit('update:reservationTo', ($event.target as HTMLInputElement).value)"
              />
              <svg class="zone-picker-date-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                <line x1="16" y1="2" x2="16" y2="6" />
                <line x1="8" y1="2" x2="8" y2="6" />
                <line x1="3" y1="10" x2="21" y2="10" />
              </svg>
            </div>
          </div>
        </div>

        <p class="zone-picker-note rt-text-muted">{{ t('desktop.zonePicker.note') }}</p>
        <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
          {{ checkoutFeedback.message }}
        </p>
      </div>

      <div v-else-if="zonePickerStep === 'payment'" class="zone-payment-form">
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

      <div v-else-if="zonePickerStep === 'success'" class="zone-success-page">
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

      <div v-else class="zone-checkout-summary">
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
    </div>

    <div class="zone-picker-actions">
      <button class="zone-picker-back-btn rt-btn rt-btn-ghost" type="button" @click="emit('back')">{{ t('common.back') }}</button>
      <button
        class="zone-picker-checkout-btn rt-btn rt-btn-primary"
        type="button"
        :disabled="!isCheckoutValid"
        @click="emit('primaryAction')"
      >
        {{ isSubmittingCheckout ? t('desktop.zonePicker.processing') : primaryActionLabel }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import type { BeachViewModel, BeachZoneViewModel } from '../../types/beaches'

defineProps<{
  isDesktop?: boolean
  selectedZoneBeach: BeachViewModel | null
  selectedZone: BeachZoneViewModel | null
  reservationName: string
  reservationFrom: string
  reservationTo: string
  todayDate: string
  selectedZonePrice: string
  zoneDailyPrice: string
  formattedDuration: string
  zonePickerStep: 'form' | 'summary' | 'payment' | 'success'
  checkoutFeedback: { type: 'success' | 'error' | 'info'; message: string } | null
  isSubmittingCheckout: boolean
  isCheckoutValid: boolean
  primaryActionLabel: string
  orderId: string | null
  locationName: string
}>()

const emit = defineEmits<{
  'update:reservationName': [value: string]
  'update:reservationFrom': [value: string]
  'update:reservationTo': [value: string]
  back: []
  primaryAction: []
}>()

const { t } = useI18n()
</script>

<style scoped>
.zone-picker-menu {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
  border-radius: 16px;
  overflow: hidden;
  background: #fafafc;
}

.zone-picker-hero {
  width: 100%;
  height: 168px;
  background: #d6dcde;
}

.zone-picker-hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.zone-picker-hero-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 56px;
}

.zone-picker-content {
  padding: clamp(12px, 2.2vw, 18px);
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
  overflow-y: auto;
}

.zone-picker-summary {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  align-items: baseline;
}

.zone-picker-title-wrap {
  display: flex;
  align-items: baseline;
  gap: 8px;
  min-width: 0;
}

.zone-picker-beach-name {
  margin: 0;
  line-height: 1.25;
  color: #3f4a4f;
  overflow-wrap: anywhere;
}

.zone-picker-zone-name {
  color: #7a888c;
  white-space: nowrap;
}

.zone-picker-price {
  line-height: 1.25;
  color: #3f4a4f;
  white-space: nowrap;
}

.zone-picker-divider {
  height: 1px;
  background: #c6d0d3;
}

.zone-picker-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.zone-picker-label {
  color: #3f4a4f;
}

.zone-picker-input {
  width: 100%;
  height: 42px;
  border-radius: 8px;
  border: 1px solid #c8d1d4;
  background: #e6eaec;
  color: #4d5c60;
  font-size: 14px;
  padding: 0 12px;
  box-sizing: border-box;
}

.zone-picker-input:focus {
  outline: 2px solid #005f6f;
  outline-offset: 1px;
}

.zone-picker-dates-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.zone-picker-date-wrap {
  position: relative;
}

.zone-picker-date {
  padding-right: 34px;
}

.zone-picker-date::-webkit-calendar-picker-indicator {
  opacity: 0;
  width: 34px;
  cursor: pointer;
}

.zone-picker-date-icon {
  position: absolute;
  right: 9px;
  top: 50%;
  transform: translateY(-50%);
  color: #4d5c60;
  pointer-events: none;
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

.zone-picker-actions {
  border-top: 1px solid #cdd6d9;
  background: #f3f4f5;
  padding: clamp(10px, 1.8vw, 14px) clamp(12px, 2.2vw, 18px);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.zone-picker-back-btn,
.zone-picker-checkout-btn {
  width: 100%;
}

.zone-picker-checkout-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}

@media (min-width: 1024px) {
  .zone-picker-menu {
    border-radius: 0 20px 20px 0;
    background: transparent;
  }

  .zone-picker-hero {
    border-radius: 0 20px 0 0;
    overflow: hidden;
  }

  .zone-picker-content {
    padding: 12px;
    background: #fafafc;
  }

  .zone-picker-actions {
    background: #f3f4f5;
    border-radius: 0 0 20px 20px;
  }
}

@media (max-width: 1023px) {
  .zone-picker-menu,
  .zone-picker-hero,
  .zone-picker-hero-image {
    border-radius: 0;
  }
}

@media (max-width: 640px) {
  .zone-picker-hero {
    height: 200px;
  }

  .zone-picker-dates-grid {
    grid-template-columns: 1fr;
  }

  .zone-picker-actions {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
