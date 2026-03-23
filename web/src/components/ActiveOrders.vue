<template>
  <div class="active-orders-page" :class="{ desktop: isDesktop }">
    <template v-if="isDesktop">
      <ActiveOrdersDesktopNavbar :initials="initials" @navigate="emit('navigate', $event)" />

      <div class="desktop-layout" role="main" :aria-label="t('desktop.orders.activeTitle')">
        <ActiveOrdersDesktopSidebar
          :total-orders="orders.length"
          :favourite-city="favouriteCity"
          :favourite-beach="favouriteBeach"
          :top-visited-beaches="topVisitedBeaches"
          @navigate="emit('navigate', $event)"
        />

        <ActiveOrdersDesktopMain
          :loading="loading"
          :error="error"
          :action-error="actionError"
          :loading-action-id="loadingActionId"
          :active-orders-view="activeOrdersView"
          @retry="fetchOrders"
          @select="selectedOrder = $event"
          @cancel="cancelOrder"
        />
      </div>
    </template>

    <template v-else>
      <ActiveOrdersMobileContent
        :loading="loading"
        :error="error"
        :action-error="actionError"
        :loading-action-id="loadingActionId"
        :active-orders-view="activeOrdersView"
        @back="emit('back')"
        @retry="fetchOrders"
        @select="openOrderDetails"
        @cancel="cancelOrder"
      />
    </template>

    <ActiveOrderDetailModal
      v-if="selectedOrderView"
      :order="selectedOrderView"
      @close="selectedOrder = null"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { deleteOrder, getOrders, type Order } from '../services/api'
import ActiveOrderDetailModal from './orders/ActiveOrderDetailModal.vue'
import ActiveOrdersDesktopNavbar from './orders/ActiveOrdersDesktopNavbar.vue'
import ActiveOrdersDesktopMain from './orders/ActiveOrdersDesktopMain.vue'
import ActiveOrdersDesktopSidebar from './orders/ActiveOrdersDesktopSidebar.vue'
import ActiveOrdersMobileContent from './orders/ActiveOrdersMobileContent.vue'
import {
  getOrderBeachName,
  getOrderCityName,
  getOrderUmbrellaNumber,
  getOrderZoneName,
} from '../composables/useOrderPresentation'
import {
  formatOrderDate,
  formatOrderTotalPrice,
  parseOrderDate,
  useOrderTimeline,
} from '../composables/useOrderTimeline'
import type { ActiveOrderView, OrderDetailView } from '../types/orders'

const { t } = useI18n()

defineProps<{
  isDesktop?: boolean
  initials?: string
}>()

const emit = defineEmits<{
  back: []
  navigate: [tab: string]
}>()

const orders = ref<Order[]>([])
const selectedOrder = ref<Order | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const loadingActionId = ref<number | null>(null)
const actionError = ref<string | null>(null)

const { activeOrders } = useOrderTimeline(orders)

const cancellableOrders = computed(() => {
  const now = new Date()
  const limit = new Date(now)
  limit.setDate(limit.getDate() + 1)

  return new Set(
    activeOrders.value
      .filter((order) => {
        const startDate = parseOrderDate(order.start_date)
        return Boolean(startDate && startDate > limit)
      })
      .map((order) => order.id)
  )
})

const activeOrdersView = computed<ActiveOrderView[]>(() => {
  return activeOrders.value.map((order) => ({
    id: order.id,
    raw: order,
    beachName: getOrderBeachName(order),
    cityName: getOrderCityName(order),
    zoneName: getOrderZoneName(order),
    umbrellaNumber: getOrderUmbrellaNumber(order),
    price: formatOrderTotalPrice(order),
    startDateFormatted: formatOrderDate(order.start_date),
    isCancellable: cancellableOrders.value.has(order.id),
  }))
})

const selectedOrderView = computed<OrderDetailView | null>(() => {
  if (!selectedOrder.value) return null

  return {
    id: selectedOrder.value.id,
    beachName: getOrderBeachName(selectedOrder.value),
    cityName: getOrderCityName(selectedOrder.value),
    zoneName: getOrderZoneName(selectedOrder.value),
    umbrellaNumber: getOrderUmbrellaNumber(selectedOrder.value),
    checkInFormatted: formatOrderDate(selectedOrder.value.start_date),
    checkOutFormatted: formatOrderDate(selectedOrder.value.end_date),
    totalPrice: formatOrderTotalPrice(selectedOrder.value),
  }
})

const favouriteCity = computed(() => {
  const counter = new Map<string, number>()
  for (const order of orders.value) {
    const city = getCityName(order)
    if (city === 'Unknown City') continue
    counter.set(city, (counter.get(city) || 0) + 1)
  }
  if (counter.size === 0) return '-'
  return [...counter.entries()].sort((a, b) => b[1] - a[1])[0][0]
})

const favouriteBeach = computed(() => {
  const counter = new Map<string, number>()
  for (const order of orders.value) {
    const beach = getBeachName(order)
    if (beach === 'Unknown Beach') continue
    counter.set(beach, (counter.get(beach) || 0) + 1)
  }
  if (counter.size === 0) return '-'
  return [...counter.entries()].sort((a, b) => b[1] - a[1])[0][0]
})

const topVisitedBeaches = computed(() => {
  const grouped = new Map<string, { name: string; city: string; count: number }>()

  for (const order of orders.value) {
    const name = getBeachName(order)
    if (name === 'Unknown Beach') continue

    const key = name.toLowerCase()
    const current = grouped.get(key)
    if (current) {
      current.count += 1
      continue
    }

    grouped.set(key, {
      name,
      city: getCityName(order),
      count: 1,
    })
  }

  return [...grouped.values()].sort((a, b) => b.count - a.count).slice(0, 3)
})

const fetchOrders = async () => {
  loading.value = true
  error.value = null
  actionError.value = null
  try {
    orders.value = await getOrders({ active: true })
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to load orders'
  } finally {
    loading.value = false
  }
}

const cancelOrder = async (orderId: number) => {
  loadingActionId.value = orderId
  actionError.value = null

  try {
    await deleteOrder(orderId)
    orders.value = orders.value.filter((order) => order.id !== orderId)
    if (selectedOrder.value?.id === orderId) {
      selectedOrder.value = null
    }
  } catch (err) {
    actionError.value = err instanceof Error ? err.message : 'Unable to cancel order'
  } finally {
    loadingActionId.value = null
  }
}

const getBeachName = (order: Order): string => {
  return getOrderBeachName(order)
}

const getCityName = (order: Order): string => {
  return getOrderCityName(order)
}

const openOrderDetails = (order: Order) => {
  selectedOrder.value = order
}

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
.active-orders-page {
  --color-primary: #005f6f;
  --color-text: #414d4f;
  --color-border: #e2e8ec;
  --color-surface: #ffffff;
  position: absolute;
  top: var(--topbar-height, 64px);
  left: 0;
  right: 0;
  bottom: calc(var(--nav-height, 80px) + env(safe-area-inset-bottom));
  padding: 16px clamp(12px, 4vw, 24px);
  background: var(--color-surface);
  overflow: auto;
  z-index: 90;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}

.active-orders-page.desktop {
  position: relative;
  inset: auto;
  top: auto;
  bottom: auto;
  padding: 0;
  height: 100vh;
  background: #f3f4f5;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.desktop-layout {
  flex: 1;
  min-height: 0;
  display: grid;
  grid-template-columns: 300px 1fr;
  overflow: hidden;
}

.active-orders-header {
  display: flex;
  align-items: center;
  gap: 12px;
  position: sticky;
  top: 0;
  z-index: 2;
  background: #ffffff;
  padding-bottom: 12px;
  margin-bottom: 8px;
}

.active-orders-back {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #414d4f;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.active-orders-back:hover {
  color: var(--color-primary);
}

.active-orders-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
}

.active-orders-subtitle {
  margin: 0 0 20px;
  font-size: 14px;
  color: #78898c;
  font-weight: 400;
}

.active-orders-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-bottom: 8px;
}
.active-order-card {
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 12px;
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.active-order-card:hover {
  border-color: #d7dee3;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
}

.active-order-card:focus-visible {
  outline: 2px solid var(--color-primary);
  outline-offset: 2px;
}

.active-order-main { min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.active-order-main h3 {
  margin: 0;
  font-size: 14px;
  color: #242b2c;
}
.active-order-main span {
  font-size: 12px;
  color: #73858a;
}

.active-order-main small {
  font-size: 11px;
  color: #6d7b80;
}

.mobile-action-btn {
  border: 0;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
}
.mobile-action-btn.active { background: #7a8d91; }
.mobile-action-btn.cancel { background: var(--color-primary); }
</style>
