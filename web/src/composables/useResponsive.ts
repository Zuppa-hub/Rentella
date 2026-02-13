/**
 * Composable for responsive design handling
 * Provides reactive breakpoint detection
 */

import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { BREAKPOINT_DESKTOP } from '../constants/index'
import { createLogger } from '../utils/logger'

const logger = createLogger('useResponsive')

export const useResponsive = () => {
  const windowWidth = ref<number>(typeof window !== 'undefined' ? window.innerWidth : 1024)

  const isDesktop = computed(() => windowWidth.value >= BREAKPOINT_DESKTOP)
  const isMobile = computed(() => !isDesktop.value)

  const handleResize = () => {
    windowWidth.value = window.innerWidth
    logger.debug('Window resized', { width: windowWidth.value })
  }

  onMounted(() => {
    // Set initial value
    windowWidth.value = window.innerWidth
    window.addEventListener('resize', handleResize, { passive: true })
  })

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize)
  })

  return {
    windowWidth,
    isDesktop,
    isMobile,
  }
}
