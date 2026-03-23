<template>
  <div class="account-details-card">
    <div class="details-row">
      <span>{{ t('desktop.settings.fullName') }}</span>
      <strong>{{ fullName || '-' }}</strong>
    </div>
    <div class="details-row">
      <span>{{ t('desktop.settings.email') }}</span>
      <strong>{{ email || '-' }}</strong>
    </div>
    <div class="details-row">
      <span>{{ t('desktop.settings.username') }}</span>
      <strong>{{ username || '-' }}</strong>
    </div>
    <button type="button" class="logout-btn" @click="emit('logout')">{{ t('desktop.settings.logout') }}</button>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  fullName?: string
  email?: string
  username?: string
}>()

const emit = defineEmits<{
  logout: []
}>()

const { t } = useI18n()
</script>

<style scoped>
.account-details-card {
  margin-top: 14px;
  border: 1px solid #d7dee2;
  border-radius: 16px;
  background: #ffffff;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.details-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 13px;
  color: #73858a;
}

.details-row strong {
  color: #2f3b3e;
}

.logout-btn {
  margin-top: 6px;
  align-self: flex-start;
  width: 100%;
  border: none;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 600;
  background: #0b5f6f;
  color: #ffffff;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.logout-btn:hover {
  opacity: 0.92;
}

.logout-btn:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

@media (min-width: 1024px) {
  .logout-btn {
    width: auto;
    align-self: flex-end;
  }
}
</style>
