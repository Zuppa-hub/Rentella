<template>
  <div class="skeleton-loader" role="status" aria-live="polite" :aria-label="ariaLabel">
    <div v-for="index in rows" :key="index" class="skeleton-line" :style="lineStyle(index)"></div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    rows?: number
    height?: number
    radius?: number
    gap?: number
    widths?: string[]
    ariaLabel?: string
  }>(),
  {
    rows: 4,
    height: 14,
    radius: 8,
    gap: 10,
    widths: () => ['100%', '96%', '92%', '88%'],
    ariaLabel: 'Loading content',
  }
)

const lineStyle = (index: number) => {
  const width = props.widths[(index - 1) % props.widths.length]
  return {
    height: `${props.height}px`,
    borderRadius: `${props.radius}px`,
    width,
  }
}

const gapPx = computed(() => `${props.gap}px`)
</script>

<style scoped>
.skeleton-loader {
  display: flex;
  flex-direction: column;
  gap: v-bind(gapPx);
  width: 100%;
}

.skeleton-line {
  position: relative;
  overflow: hidden;
  background: #e5eaec;
}

.skeleton-line::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: translateX(-100%);
  background: linear-gradient(
    90deg,
    rgba(229, 234, 236, 0) 0%,
    rgba(255, 255, 255, 0.7) 50%,
    rgba(229, 234, 236, 0) 100%
  );
  animation: shimmer 1.25s infinite;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}
</style>
