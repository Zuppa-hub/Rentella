import { ref, onUnmounted } from 'vue'

export function useDebounce<T extends (...args: any[]) => any>(
  callback: T,
  delay: number = 300
) {
  const timerId = ref<number | undefined>(undefined)

  const debouncedFunction = (...args: Parameters<T>) => {
    if (timerId.value !== undefined) {
      clearTimeout(timerId.value)
    }

    timerId.value = window.setTimeout(() => {
      callback(...args)
    }, delay)
  }

  const cancel = () => {
    if (timerId.value !== undefined) {
      clearTimeout(timerId.value)
      timerId.value = undefined
    }
  }

  // Cleanup on component unmount
  onUnmounted(() => {
    cancel()
  })

  return {
    debouncedFunction,
    cancel
  }
}
