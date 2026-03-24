<template>
  <div class="order-history-page" :class="{ desktop: isDesktop }">
    <template v-if="isDesktop">
      <OrderHistoryDesktopNavbar :initials="initials" @navigate="emit('navigate', $event)" />

      <div class="desktop-layout" role="main" :aria-label="t('desktop.orders.historyTitle')">
        <OrderHistoryDesktopSidebar
          :total-orders="finishedOrdersView.length"
          :favourite-city="favouriteCity"
          :favourite-beach="favouriteBeach"
          @navigate="emit('navigate', $event)"
        />

        <OrderHistoryDesktopMain
          :loading="loading"
          :error="error"
          :finished-orders-view="finishedOrdersView"
          @retry="fetchOrders"
          @select="selectOrder"
        />
      </div>
    </template>

    <template v-else>
      <OrderHistoryMobileContent
        :loading="loading"
        :error="error"
        :finished-orders-view="finishedOrdersView"
        @back="emit('back')"
        @retry="fetchOrders"
        @select="selectOrder"
      />
    </template>

    <OrderHistoryDetailModal
      v-if="selectedOrderView"
      :order="selectedOrderView"
      :is-desktop="isDesktop"
      @close="selectedOrder = null"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getOrders, type Order } from '../services/api'
import OrderHistoryDesktopMain from './orders/OrderHistoryDesktopMain.vue'
import OrderHistoryDesktopNavbar from './orders/OrderHistoryDesktopNavbar.vue'
import OrderHistoryDesktopSidebar from './orders/OrderHistoryDesktopSidebar.vue'
import OrderHistoryDetailModal from './orders/OrderHistoryDetailModal.vue'
import OrderHistoryMobileContent from './orders/OrderHistoryMobileContent.vue'
import {
  getOrderBeachName,
  getOrderCityName,
  getOrderUmbrellaNumber,
  getOrderZoneName,
} from '../composables/useOrderPresentation'
import { formatOrderDate, formatOrderTotalPrice, useOrderTimeline } from '../composables/useOrderTimeline'
import type { HistoryOrderDetailView, HistoryOrderView } from '../types/orders'

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

const { finishedOrders } = useOrderTimeline(orders)

const finishedOrdersView = computed<HistoryOrderView[]>(() => {
  return finishedOrders.value.map((order) => ({
    id: order.id,
    raw: order,
    beachName: getOrderBeachName(order),
    cityName: getOrderCityName(order),
    zoneName: getOrderZoneName(order),
    umbrellaNumber: getOrderUmbrellaNumber(order),
    price: formatOrderTotalPrice(order),
    startDateFormatted: formatOrderDate(order.start_date),
  }))
})

const selectedOrderView = computed<HistoryOrderDetailView | null>(() => {
  if (!selectedOrder.value) return null

  return {
    id: selectedOrder.value.id,
    beachName: getOrderBeachName(selectedOrder.value),
    cityName: getOrderCityName(selectedOrder.value),
    zoneName: getOrderZoneName(selectedOrder.value),
    checkInFormatted: formatOrderDate(selectedOrder.value.start_date),
    checkOutFormatted: formatOrderDate(selectedOrder.value.end_date),
    totalPrice: formatOrderTotalPrice(selectedOrder.value),
  }
})

const fetchOrders = async () => {
  loading.value = true
  error.value = null
  try {
    orders.value = await getOrders({ active: false })
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to load orders'
  } finally {
    loading.value = false
  }
}

const selectOrder = (order: Order) => {
  selectedOrder.value = order
}

const onKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && selectedOrder.value) {
    selectedOrder.value = null
  }
}

const favouriteCity = computed(() => {
  if (finishedOrdersView.value.length === 0) return '-'

  const counter = new Map<string, number>()
  for (const order of finishedOrdersView.value) {
    const city = order.cityName
    if (city === 'Unknown City') continue
    counter.set(city, (counter.get(city) || 0) + 1)
  }

  if (counter.size === 0) return '-'

  return [...counter.entries()].sort((a, b) => b[1] - a[1])[0][0]
})

const favouriteBeach = computed(() => {
  if (finishedOrdersView.value.length === 0) return '-'

  const counter = new Map<string, number>()
  for (const order of finishedOrdersView.value) {
    const beach = order.beachName
    if (beach === 'Unknown Beach') continue
    counter.set(beach, (counter.get(beach) || 0) + 1)
  }

  if (counter.size === 0) return '-'

  return [...counter.entries()].sort((a, b) => b[1] - a[1])[0][0]
})

onMounted(() => {
  fetchOrders()
  window.addEventListener('keydown', onKeyDown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', onKeyDown)
})
</script>

<style scoped>
.order-history-page {
  --color-primary: #005f6f;
  --color-text: #414d4f;
  --color-border: #c9d1d4;
  position: absolute;
  top: var(--topbar-height, 64px);
  left: 0;
  right: 0;
  bottom: calc(var(--nav-height, 80px) + env(safe-area-inset-bottom));
  width: 100%;
  height: auto;
  display: flex;
  flex-direction: column;
  background: #fff;
  font-family: 'Inter', sans-serif;
  padding: 16px clamp(12px, 4vw, 24px) 16px;
  box-sizing: border-box;
  z-index: 90;
  overflow: auto;
}

.order-history-page.desktop {
  position: relative;
  padding: 0;
  inset: auto;
  height: 100vh;
  background: #f3f4f5;
  overflow: hidden;
}

.desktop-layout {
  flex: 1;
  min-height: 0;
  display: grid;
  grid-template-columns: 300px 1fr;
  overflow: hidden;
}
</style>
