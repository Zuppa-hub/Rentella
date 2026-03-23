<template>
  <button class="map-location-indicator" :class="{ active: hasLocation }" @click="emit('center-map')" :disabled="!hasLocation">
    <span class="location-dot" :class="{ pulse: hasLocation }"></span>
    <span class="location-text">
      {{ hasLocation ? t('desktop.map.myLocation') : t('desktop.map.loading') }}
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps<{
  location?: { lat: number; lng: number } | null
}>()

const emit = defineEmits<{
  (event: 'center-map'): void
}>()

const { t } = useI18n()

const hasLocation = computed(() => !!props.location)
</script>

<style scoped>
.map-location-indicator {
  position: absolute;
  top: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.95);
  border: none;
  border-radius: 12px;
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #414d4f;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
  z-index: 999;
  font-family: 'Inter', sans-serif;
  backdrop-filter: blur(4px);
  transition: all 0.3s ease;
  cursor: pointer;
}

.map-location-indicator:hover:not(:disabled) {
  background: rgba(255, 255, 255, 1);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.16);
  transform: translateY(-1px);
}

.map-location-indicator:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.location-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #6b7280;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.location-dot.pulse {
  background: #00a8cc;
  box-shadow: 0 0 0 3px rgba(0, 168, 204, 0.2);
  animation: pulse-ring 2s infinite;
}

@keyframes pulse-ring {
  0% {
    box-shadow: 0 0 0 3px rgba(0, 168, 204, 0.2);
  }
  70% {
    box-shadow: 0 0 0 8px rgba(0, 168, 204, 0);
  }
  100% {
    box-shadow: 0 0 0 3px rgba(0, 168, 204, 0);
  }
}

.location-text {
  white-space: nowrap;
}
</style>
