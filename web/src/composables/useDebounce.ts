import { ref, onUnmounted } from 'vue'
import { debounce } from '../utils/timing'

export function useDebounce<T extends (...args: unknown[]) => void>(callback: T, delay: number = 300) {
  const isCancelled = ref(false)

  const debounced = debounce((...args: unknown[]) => {
    if (!isCancelled.value) {
      callback(...(args as Parameters<T>))
    }
  }, delay)

  const debouncedFunction = (...args: Parameters<T>) => {
    if (isCancelled.value) return
    debounced(...args)
  }

  const cancel = () => {
    isCancelled.value = true
  }

  onUnmounted(() => {
    cancel()
  })

  return {
    debouncedFunction,
    cancel,
  }
}
