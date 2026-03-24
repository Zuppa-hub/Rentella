<template>
  <div class="zone-picker-menu">
    <div class="zone-picker-hero">
      <img
        v-if="selectedZoneBeach?.photo_url"
        :src="selectedZoneBeach.photo_url"
        :alt="selectedZoneBeach.name"
        class="zone-picker-hero-image"
        loading="lazy"
      />
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

      <ZonePickerFormStep
        v-if="zonePickerStep === 'form'"
        :reservation-name="reservationName"
        :reservation-from="reservationFrom"
        :reservation-to="reservationTo"
        :today-date="todayDate"
        :checkout-feedback="checkoutFeedback"
        @update:reservation-name="emit('update:reservationName', $event)"
        @update:reservation-from="emit('update:reservationFrom', $event)"
        @update:reservation-to="emit('update:reservationTo', $event)"
      />

      <ZonePickerPaymentStep
        v-else-if="zonePickerStep === 'payment'"
        :selected-zone-price="selectedZonePrice"
        :checkout-feedback="checkoutFeedback"
      />

      <ZonePickerSuccessStep
        v-else-if="zonePickerStep === 'success'"
        :order-id="orderId"
        :location-name="locationName"
        :selected-zone-beach="selectedZoneBeach"
        :selected-zone="selectedZone"
        :reservation-from="reservationFrom"
        :reservation-to="reservationTo"
        :selected-zone-price="selectedZonePrice"
      />

      <ZonePickerSummaryStep
        v-else
        :location-name="locationName"
        :selected-zone-beach="selectedZoneBeach"
        :selected-zone="selectedZone"
        :zone-daily-price="zoneDailyPrice"
        :formatted-duration="formattedDuration"
        :selected-zone-price="selectedZonePrice"
        :checkout-feedback="checkoutFeedback"
      />
    </div>

    <div class="zone-picker-actions">
      <button class="zone-picker-back-btn rt-btn rt-btn-ghost" type="button" @click="emit('back')">
        {{ t('common.back') }}
      </button>
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
import ZonePickerFormStep from './zone-picker/ZonePickerFormStep.vue'
import ZonePickerPaymentStep from './zone-picker/ZonePickerPaymentStep.vue'
import ZonePickerSuccessStep from './zone-picker/ZonePickerSuccessStep.vue'
import ZonePickerSummaryStep from './zone-picker/ZonePickerSummaryStep.vue'
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

  .zone-picker-actions {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
