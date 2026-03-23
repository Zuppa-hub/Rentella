<template>
  <div class="active-orders-page" :class="{ desktop: isDesktop }">
    <template v-if="isDesktop">
      <nav class="navbar">
        <div class="navbar-container">
          <div class="logo-section">
            <img :src="icons.logo" alt="Rentella" class="logo" />
          </div>
          <div class="nav-items">
            <button class="nav-item" type="button" @click="emit('navigate', 'home')">
              <img :src="icons.home" alt="" class="nav-icon" />
              <span>Home</span>
            </button>
            <button class="nav-item active" type="button" aria-current="page">
              <img :src="icons.active" alt="" class="nav-icon" />
              <span>Active</span>
            </button>
            <button class="nav-item" type="button" @click="emit('navigate', 'history')">
              <img :src="icons.history" alt="" class="nav-icon" />
              <span>History</span>
            </button>
            <button class="nav-item" type="button" @click="emit('navigate', 'settings')">
              <img :src="icons.settings" alt="" class="nav-icon" />
              <span>Settings</span>
            </button>
          </div>
          <div class="profile-section">
            <div class="profile-avatar">{{ initials || 'JD' }}</div>
          </div>
        </div>
      </nav>

      <div class="desktop-layout" role="main" aria-label="Active orders desktop layout">
        <aside class="desktop-sidebar">
          <h1 class="desktop-sidebar-title">Active Orders</h1>

          <div class="desktop-metrics">
            <div class="desktop-metric-row">
              <span>Total Orders</span>
              <strong>{{ orders.length }}</strong>
            </div>
            <div class="desktop-metric-row">
              <span>Favourite City</span>
              <strong>{{ favouriteCity }}</strong>
            </div>
            <div class="desktop-metric-row">
              <span>Favourite Beach</span>
              <strong>{{ favouriteBeach }}</strong>
            </div>
          </div>

          <div class="desktop-most-visited">
            <h2>Most Visited Beaches</h2>
            <div class="desktop-most-visited-list">
              <div v-for="(item, index) in topVisitedBeaches" :key="item.name" class="desktop-most-visited-item">
                <span class="desktop-most-visited-rank">{{ index + 1 }}.</span>
                <div class="desktop-most-visited-card">
                  <div class="desktop-most-visited-thumb" aria-hidden="true">🏖️</div>
                  <div class="desktop-most-visited-content">
                    <strong>{{ item.name }}</strong>
                    <span>{{ item.city }} • {{ item.count }} orders</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="desktop-history-link-wrap">
            <p>Looking for previous orders?</p>
            <button type="button" class="desktop-history-link" @click="emit('navigate', 'history')">History</button>
          </div>
        </aside>

        <section class="desktop-main" aria-label="Active orders">
          <h2 class="desktop-main-title">Active orders</h2>

          <div v-if="loading" class="active-orders-loading" aria-live="polite">
            <p>Loading orders...</p>
          </div>

          <div v-else-if="error" class="active-orders-error" role="alert">
            <p>{{ error }}</p>
            <button @click="fetchOrders" class="rt-btn rt-btn-primary">Retry</button>
          </div>

          <div v-else-if="activeOrders.length === 0" class="active-orders-empty">
            <p>No active orders found</p>
          </div>

          <div v-else class="desktop-orders-list" role="list" aria-label="Active orders list">
            <button
              v-for="order in activeOrders"
              :key="order.id"
              type="button"
              class="desktop-order-row"
              @click="selectedOrder = order"
              :aria-label="`Open active order ${order.id} details`"
              role="listitem"
            >
              <div class="desktop-order-col desktop-order-col-beach">
                <span class="desktop-order-label">Beach Name</span>
                <strong>{{ getBeachName(order) }}</strong>
              </div>
              <div class="desktop-order-col">
                <span class="desktop-order-label">Order ID</span>
                <strong>#{{ order.id }}</strong>
              </div>
              <div class="desktop-order-col">
                <span class="desktop-order-label">Section</span>
                <strong>{{ getZoneName(order) }}</strong>
              </div>
              <div class="desktop-order-col">
                <span class="desktop-order-label">Umbrella</span>
                <strong>{{ getUmbrellaNumber(order) }}</strong>
              </div>
              <div class="desktop-order-col">
                <span class="desktop-order-label">Price</span>
                <strong>{{ getPrice(order) }}</strong>
              </div>
              <div class="desktop-order-col">
                <span class="desktop-order-label">Date</span>
                <strong>{{ formatDate(order.start_date) }}</strong>
              </div>
              <div class="desktop-order-action">
                <button
                  type="button"
                  class="desktop-action-btn"
                  :class="{ cancel: isOrderCancellable(order), active: !isOrderCancellable(order) }"
                  :disabled="!isOrderCancellable(order) || loadingActionId === order.id"
                  @click.stop="cancelOrder(order.id)"
                >
                  {{ loadingActionId === order.id ? 'Cancelling...' : isOrderCancellable(order) ? 'Cancel Order' : 'Active' }}
                </button>
              </div>
            </button>

            <p v-if="actionError" class="desktop-action-error" role="alert">{{ actionError }}</p>
            <p class="desktop-records-count">{{ activeOrders.length }} Records Shown</p>
          </div>
        </section>
      </div>
    </template>

    <template v-else>
      <div class="active-orders-header">
        <button class="active-orders-back" @click="emit('back')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        <h1 class="active-orders-title">Active Orders</h1>
      </div>

      <p class="active-orders-subtitle">See your upcoming bookings</p>

      <div v-if="loading" class="active-orders-loading" aria-live="polite">
        <p>Loading orders...</p>
      </div>

      <div v-else-if="error" class="active-orders-error" role="alert">
        <p>{{ error }}</p>
        <button @click="fetchOrders" class="rt-btn rt-btn-primary">Retry</button>
      </div>

      <div v-else-if="activeOrders.length === 0" class="active-orders-empty">
        <p>No active orders found</p>
      </div>

      <div v-else class="active-orders-list" role="list" aria-label="Active orders list">
        <div
          v-for="order in activeOrders"
          :key="order.id"
          class="active-order-card"
          @click="openOrderDetails(order)"
          @keydown.enter.prevent="openOrderDetails(order)"
          @keydown.space.prevent="openOrderDetails(order)"
          :aria-label="`Open active order ${order.id} details`"
          role="listitem"
          tabindex="0"
        >
          <div class="active-order-main">
            <h3>{{ getBeachName(order) }}</h3>
            <span>{{ getCityName(order) }}</span>
            <small>{{ getZoneName(order) }} • {{ getPrice(order) }} • {{ formatDate(order.start_date) }}</small>
          </div>
          <button
            type="button"
            class="mobile-action-btn"
            :class="{ cancel: isOrderCancellable(order), active: !isOrderCancellable(order) }"
            :disabled="!isOrderCancellable(order) || loadingActionId === order.id"
            @click.stop="cancelOrder(order.id)"
          >
            {{ loadingActionId === order.id ? 'Cancelling...' : isOrderCancellable(order) ? 'Cancel' : 'Active' }}
          </button>
        </div>
      </div>

      <p v-if="actionError" class="desktop-action-error" role="alert">{{ actionError }}</p>
    </template>

    <div v-if="selectedOrder" class="order-detail-modal-overlay" @click="selectedOrder = null" role="dialog" aria-modal="true" aria-label="Order details">
      <div class="order-detail-modal" @click.stop>
        <button class="order-detail-close" @click="selectedOrder = null" aria-label="Close order details">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <h3 class="order-detail-title">{{ getBeachName(selectedOrder) }}</h3>
        <div class="order-detail-content">
          <div class="order-detail-row"><span>Order ID:</span><strong>#{{ selectedOrder.id }}</strong></div>
          <div class="order-detail-row"><span>Location:</span><strong>{{ getCityName(selectedOrder) }}</strong></div>
          <div class="order-detail-row"><span>Zone:</span><strong>{{ getZoneName(selectedOrder) }}</strong></div>
          <div class="order-detail-row"><span>Umbrella:</span><strong>{{ getUmbrellaNumber(selectedOrder) }}</strong></div>
          <div class="order-detail-row"><span>Check-in:</span><strong>{{ formatDate(selectedOrder.start_date) }}</strong></div>
          <div class="order-detail-row"><span>Check-out:</span><strong>{{ formatDate(selectedOrder.end_date) }}</strong></div>
          <div class="order-detail-row"><span>Total Price:</span><strong>{{ getPrice(selectedOrder) }}</strong></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { deleteOrder, getOrders, type Order } from '../services/api'
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
import logoDark from '../assets/LogoDark.svg'
import homeIcon from '../assets/icons/Home.svg'
import activeIcon from '../assets/icons/Active.svg'
import historyIcon from '../assets/icons/History.svg'
import settingsIcon from '../assets/icons/Settings.svg'

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

const icons = {
  logo: logoDark,
  home: homeIcon,
  active: activeIcon,
  history: historyIcon,
  settings: settingsIcon,
}

const { activeOrders } = useOrderTimeline(orders)

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

const isOrderCancellable = (order: Order): boolean => {
  const startDate = parseOrderDate(order.start_date)
  if (!startDate) return false

  const now = new Date()
  const limit = new Date(now)
  limit.setDate(limit.getDate() + 1)

  return startDate > limit
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

const getZoneName = (order: Order): string => {
  return getOrderZoneName(order)
}

const getUmbrellaNumber = (order: Order): string => {
  return getOrderUmbrellaNumber(order)
}

const getPrice = (order: Order): string => {
  return formatOrderTotalPrice(order)
}

const formatDate = (dateStr: string): string => {
  return formatOrderDate(dateStr)
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
  position: absolute;
  top: var(--topbar-height, 64px);
  left: 0;
  right: 0;
  bottom: calc(var(--nav-height, 80px) + env(safe-area-inset-bottom));
  padding: 16px clamp(12px, 4vw, 24px);
  background: #ffffff;
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

.navbar {
  height: 72px;
  background: #005f6f;
  box-shadow: 0 -4px 8px rgba(85, 85, 85, 0.08);
  display: flex;
  align-items: center;
  padding: 0 32px;
}

.navbar-container {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 16px;
}

.logo { height: 32px; width: auto; }
.nav-items { display: flex; margin-left: auto; margin-right: 12px; }
.nav-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 16px;
  height: 100%;
  border: 0;
  background: transparent;
  color: #d2eef1;
  font-size: 11px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  transition: opacity 0.3s ease;
}
.nav-item:hover { opacity: 0.8; }
.nav-item.active { color: #fff; }
.nav-icon { width: 22px; height: 22px; }
.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f4f6;
  color: #1f2937;
  display: grid;
  place-items: center;
  font-size: 14px;
  font-weight: 600;
}

.desktop-layout {
  flex: 1;
  min-height: 0;
  display: grid;
  grid-template-columns: 300px 1fr;
  overflow: hidden;
}

.desktop-sidebar {
  border-right: 1px solid #c9d1d4;
  padding: 32px 24px;
  background: #f3f4f5;
  overflow-y: auto;
  min-height: 0;
  display: flex;
  flex-direction: column;
}

.desktop-sidebar-title {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #414d4f;
}

.desktop-sidebar-title::after {
  content: '';
  display: block;
  margin-top: 14px;
  height: 1px;
  background: #c9d1d4;
}

.desktop-metrics {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.desktop-metric-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 12px;
  color: #73858a;
}

.desktop-metric-row strong {
  font-size: 13px;
  color: #414d4f;
}

.desktop-most-visited { margin-top: 28px; }
.desktop-most-visited h2 {
  margin: 0 0 12px;
  font-size: 14px;
  font-weight: 700;
  color: #414d4f;
}

.desktop-most-visited-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.desktop-most-visited-item {
  display: grid;
  grid-template-columns: 22px 1fr;
  align-items: center;
  gap: 8px;
}

.desktop-most-visited-rank {
  font-size: 16px;
  font-weight: 700;
  color: #57676c;
}

.desktop-most-visited-card {
  border: 1px solid #e2e8ec;
  border-radius: 14px;
  background: #ffffff;
  padding: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.desktop-most-visited-thumb {
  width: 56px;
  height: 56px;
  border-radius: 10px;
  background: #f0f4f6;
  display: grid;
  place-items: center;
  font-size: 24px;
}

.desktop-most-visited-content {
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.desktop-most-visited-content strong {
  font-size: 12px;
  color: #2a3436;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-most-visited-content span {
  font-size: 11px;
  color: #73858a;
}

.desktop-history-link-wrap {
  margin-top: auto;
  padding-top: 18px;
  text-align: center;
  font-size: 12px;
  color: #73858a;
}

.desktop-history-link {
  border: 0;
  background: transparent;
  color: #414d4f;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
}

.desktop-main {
  padding: 24px 24px 18px;
  display: flex;
  flex-direction: column;
  min-width: 0;
  min-height: 0;
  overflow: hidden;
}

.desktop-main-title {
  margin: 0 0 18px;
  font-size: 20px;
  line-height: 1.2;
  font-weight: 600;
  color: #414d4f;
}

.desktop-orders-list {
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.desktop-order-row {
  border: 1px solid #e2e8ec;
  border-radius: 16px;
  background: #fff;
  padding: 12px 14px;
  display: grid;
  grid-template-columns: 2.8fr 1.1fr 1.1fr 1fr 1fr 1fr 1.6fr;
  gap: 8px;
  text-align: left;
  cursor: pointer;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
  border: 1px solid #e5eaee;
}

.desktop-order-col { min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.desktop-order-label { font-size: 11px; color: #73858a; font-weight: 600; }
.desktop-order-col strong {
  font-size: 15px;
  color: #414d4f;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-order-action {
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.desktop-action-btn {
  border: 0;
  border-radius: 8px;
  padding: 10px 14px;
  min-width: 122px;
  font-size: 13px;
  font-weight: 700;
  color: #fff;
}

.desktop-action-btn.active {
  background: #7a8d91;
  cursor: default;
}

.desktop-action-btn.cancel {
  background: #005f6f;
  cursor: pointer;
}

.desktop-action-btn:disabled {
  opacity: 0.95;
}

.desktop-records-count {
  margin: 12px 0 0;
  text-align: center;
  font-size: 13px;
  color: #6f7c81;
}

.desktop-action-error {
  margin: 6px 0 0;
  font-size: 12px;
  color: #b42318;
  text-align: center;
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
  color: #005f6f;
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
  outline: 2px solid #005f6f;
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
.mobile-action-btn.cancel { background: #005f6f; }

.active-orders-loading,
.active-orders-error,
.active-orders-empty {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 12px;
}

.order-detail-modal-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 2000;
}

.order-detail-modal {
  width: min(560px, 100%);
  max-height: 100%;
  overflow: auto;
  background: #fff;
  border-radius: 18px;
  padding: 20px;
  position: relative;
}

.order-detail-close {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 32px;
  height: 32px;
  border: 0;
  border-radius: 999px;
  background: #f3f4f6;
  color: #414d4f;
  display: grid;
  place-items: center;
}

.order-detail-title {
  margin: 0 36px 14px 0;
  font-size: 18px;
  color: #242b2c;
}

.order-detail-content {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 13px;
  color: #5b6b70;
}

.order-detail-row strong {
  color: #2a3436;
}
</style>
