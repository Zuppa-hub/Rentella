import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Order } from '@/types'
import { apiClient } from '@/services/api'

export const useOrderStore = defineStore('order', () => {
  const orders = ref<Order[]>([])
  const currentOrder = ref<Order | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const orderCount = computed(() => orders.value.length)
  const activeOrders = computed(() =>
    orders.value.filter((o) => o.status !== 'cancelled' && o.status !== 'completed')
  )

  async function fetchOrders() {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get('/orders')
      orders.value = response.data.data || response.data
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch orders'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function fetchOrderById(id: number) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.get(`/orders/${id}`)
      currentOrder.value = response.data.data || response.data
      return currentOrder.value
    } catch (err: any) {
      error.value = err.message || 'Failed to fetch order'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  async function createOrder(orderData: Partial<Order>) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.post('/orders', orderData)
      const newOrder = response.data.data || response.data
      orders.value.push(newOrder)
      currentOrder.value = newOrder
      return newOrder
    } catch (err: any) {
      error.value = err.message || 'Failed to create order'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateOrder(id: number, orderData: Partial<Order>) {
    loading.value = true
    error.value = null
    try {
      const response = await apiClient.put(`/orders/${id}`, orderData)
      const updated = response.data.data || response.data
      const index = orders.value.findIndex((o) => o.id === id)
      if (index !== -1) {
        orders.value[index] = updated
      }
      if (currentOrder.value?.id === id) {
        currentOrder.value = updated
      }
      return updated
    } catch (err: any) {
      error.value = err.message || 'Failed to update order'
      console.error(err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function cancelOrder(id: number) {
    return updateOrder(id, { status: 'cancelled' })
  }

  function clearError() {
    error.value = null
  }

  function reset() {
    orders.value = []
    currentOrder.value = null
    error.value = null
  }

  return {
    orders,
    currentOrder,
    loading,
    error,
    orderCount,
    activeOrders,
    fetchOrders,
    fetchOrderById,
    createOrder,
    updateOrder,
    cancelOrder,
    clearError,
    reset,
  }
})
