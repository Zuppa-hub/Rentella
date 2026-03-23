<template>
  <div class="zone-picker-form">
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
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  reservationName: string
  reservationFrom: string
  reservationTo: string
  todayDate: string
  checkoutFeedback: { type: 'success' | 'error' | 'info'; message: string } | null
}>()

const emit = defineEmits<{
  'update:reservationName': [value: string]
  'update:reservationFrom': [value: string]
  'update:reservationTo': [value: string]
}>()

const { t } = useI18n()
</script>

<style scoped>
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

@media (max-width: 640px) {
  .zone-picker-dates-grid {
    grid-template-columns: 1fr;
  }
}
</style>
