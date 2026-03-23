<template>
  <div class="order-history-page" :class="{ desktop: isDesktop }">
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
            <button class="nav-item" type="button" @click="emit('navigate', 'active')">
              <img :src="icons.active" alt="" class="nav-icon" />
              <span>Active</span>
            </button>
            <button class="nav-item active" type="button" aria-current="page">
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

      <div class="desktop-layout" role="main" aria-label="Order history desktop layout">
        <aside class="desktop-sidebar">
          <h1 class="desktop-sidebar-title">Order History</h1>

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

          <div class="desktop-active-link-wrap">
            <p>Looking for Active Orders?</p>
            <button type="button" class="desktop-active-link" @click="emit('navigate', 'active')">Active</button>
          </div>
        </aside>

        <section class="desktop-main" aria-label="Previous purchases">
          <h2 class="desktop-main-title">Previous Purchases</h2>

          <div v-if="loading" class="order-history-loading" aria-live="polite">
            <p>Loading orders...</p>
          </div>

          <div v-else-if="error" class="order-history-error" role="alert">
            <p>{{ error }}</p>
            <button @click="fetchOrders" class="rt-btn rt-btn-primary">Retry</button>
          </div>

          <div v-else-if="orders.length === 0" class="order-history-empty">
            <p>No orders found</p>
          </div>

          <div v-else class="desktop-orders-list" role="list" aria-label="Orders list">
            <button
              v-for="order in orders"
              :key="order.id"
              type="button"
              class="desktop-order-row"
              @click="selectOrder(order)"
              :aria-label="`Open order ${order.id} details`"
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
            </button>

            <p class="desktop-records-count">{{ orders.length }} Records Shown</p>
          </div>
        </section>
      </div>
    </template>

    <template v-else>
      <div class="order-history-header">
        <button class="order-history-back" @click="emit('back')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        <h1 class="order-history-title">Order History</h1>
      </div>

      <p class="order-history-subtitle">See all your past purchases</p>

      <div v-if="loading" class="order-history-loading" aria-live="polite">
        <p>Loading orders...</p>
      </div>

      <div v-else-if="error" class="order-history-error" role="alert">
        <p>{{ error }}</p>
        <button @click="fetchOrders" class="rt-btn rt-btn-primary">Retry</button>
      </div>

      <div v-else-if="orders.length === 0" class="order-history-empty">
        <p>No orders found</p>
      </div>

      <div v-else class="order-history-list" role="list" aria-label="Orders list">
        <button
          v-for="order in orders"
          :key="order.id"
          class="order-card"
          type="button"
          @click="selectOrder(order)"
          :aria-label="`Open order ${order.id} details`"
          role="listitem"
        >
          <div class="order-card-content">
            <div class="order-card-main">
              <h3 class="order-card-beach">{{ getBeachName(order) }}</h3>
              <span class="order-card-city">{{ getCityName(order) }}</span>
            </div>
            <span class="order-card-date">{{ formatDate(order.start_date) }}</span>
          </div>
          <svg class="order-card-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="9 18 15 12 9 6"></polyline>
          </svg>
        </button>
      </div>

      <p v-if="!loading && orders.length > 0" class="order-history-count">{{ orders.length }} Records</p>
    </template>

    <div
      v-if="selectedOrder"
      class="order-detail-modal-overlay"
      :class="{ desktop: isDesktop }"
      @click="selectedOrder = null"
      role="dialog"
      aria-modal="true"
      aria-label="Order details"
    >
      <div class="order-detail-modal" :class="{ desktop: isDesktop }" @click.stop>
        <button class="order-detail-close" @click="selectedOrder = null" aria-label="Close order details">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <h3 class="order-detail-title">{{ getBeachName(selectedOrder) }}</h3>
        <div class="order-detail-content">
          <div class="order-detail-row">
            <span class="order-detail-label">Order ID:</span>
            <span class="order-detail-value">#{{ selectedOrder.id }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Location:</span>
            <span class="order-detail-value">{{ getCityName(selectedOrder) }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Zone:</span>
            <span class="order-detail-value">{{ getZoneName(selectedOrder) }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Check-in:</span>
            <span class="order-detail-value">{{ formatDate(selectedOrder.start_date) }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Check-out:</span>
            <span class="order-detail-value">{{ formatDate(selectedOrder.end_date) }}</span>
          </div>
          <div class="order-detail-row order-detail-row-total">
            <span class="order-detail-label">Total Price:</span>
            <span class="order-detail-value">{{ getPrice(selectedOrder) }}</span>
          </div>
        </div>
        <button class="order-detail-close-btn rt-btn rt-btn-primary" @click="selectedOrder = null">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { getOrders, type Order } from '../services/api'
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

const icons = {
  logo: logoDark,
  home: homeIcon,
  active: activeIcon,
  history: historyIcon,
  settings: settingsIcon,
}

const fetchOrders = async () => {
  loading.value = true
  error.value = null
  try {
    orders.value = await getOrders()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Failed to load orders'
    console.error('Failed to fetch orders:', err)
  } finally {
    loading.value = false
  }
}

const getBeachName = (order: Order): string => {
  return order.umbrella?.zone?.beach?.name || 'Unknown Beach'
}

const getCityName = (order: Order): string => {
  return order.umbrella?.zone?.beach?.city_location?.city_name || 'Unknown City'
}

const getZoneName = (order: Order): string => {
  return order.umbrella?.zone?.name || 'Unknown Zone'
}

const getUmbrellaNumber = (order: Order): string => {
  return order.umbrella?.number != null ? String(order.umbrella.number) : '-'
}

const getPrice = (order: Order): string => {
  const price = order.price?.price
  return price != null ? `${price}€` : 'N/A'
}

const formatDate = (dateStr: string): string => {
  try {
    const [year, month, day] = dateStr.split('-')
    return `${day}.${month}.${year}`
  } catch {
    return dateStr
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
  if (orders.value.length === 0) return '-'

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
  if (orders.value.length === 0) return '-'

  const counter = new Map<string, number>()
  for (const order of orders.value) {
    const beach = getBeachName(order)
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
  position: absolute;
  top: var(--topbar-height, 64px);
  left: 0;
  right: 0;
  bottom: calc(var(--nav-height, 80px) + env(safe-area-inset-bottom));
  width: 100%;
  height: auto;
  display: flex;
  flex-direction: column;
  background: #ffffff;
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

.navbar {
  height: 72px;
  background: #005f6f;
  box-shadow: 0px -4px 8px rgba(85, 85, 85, 0.08);
  display: flex;
  align-items: center;
  padding: 0 32px;
  flex-shrink: 0;
}

.navbar-container {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 16px;
}

.logo-section {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
}

.logo {
  height: 32px;
  width: auto;
}

.nav-items {
  display: flex;
  gap: 0;
  flex: 0 0 auto;
  justify-content: flex-end;
  margin-left: auto;
  margin-right: 12px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 16px;
  height: 100%;
  cursor: pointer;
  color: #d2eef1;
  font-size: 11px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  transition: opacity 0.3s ease;
  border: 0;
  background: transparent;
}

.nav-item.active {
  color: #ffffff;
}

.nav-item:hover {
  opacity: 0.8;
}

.nav-icon {
  width: 22px;
  height: 22px;
}

.profile-section {
  display: flex;
  align-items: center;
  gap: 16px;
}

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f4f6;
  color: #1f2937;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
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
  line-height: 1.2;
  font-weight: 600;
  color: #414d4f;
}

.desktop-sidebar-title::after {
  content: '';
  display: block;
  margin-top: 16px;
  height: 1px;
  background: #c9d1d4;
}

.desktop-metrics {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.desktop-metric-row {
  display: flex;
  justify-content: space-between;
  gap: 16px;
  color: #73858a;
  font-size: 12px;
  line-height: 1.2;
}

.desktop-metric-row strong {
  color: #414d4f;
  font-size: 13px;
  font-weight: 700;
}

.desktop-active-link-wrap {
  margin-top: auto;
  padding-top: 28px;
  text-align: center;
  color: #73858a;
  font-size: 12px;
}

.desktop-active-link-wrap p {
  margin: 0 0 8px;
}

.desktop-active-link {
  border: 0;
  background: transparent;
  font-size: 14px;
  line-height: 1.2;
  font-weight: 700;
  color: #414d4f;
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
  color: #414d4f;
  font-weight: 600;
}

.desktop-orders-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-right: 4px;
}

.desktop-order-row {
  border: 1px solid #e2e8ec;
  border-radius: 16px;
  background: #ffffff;
  display: grid;
  grid-template-columns: 2.8fr 1.2fr 1.2fr 1.1fr 1fr 1fr;
  gap: 10px;
  padding: 14px 16px;
  text-align: left;
  cursor: pointer;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.05);
  transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.desktop-order-row:hover {
  background: #f8fafb;
  box-shadow: 0 10px 20px rgba(15, 23, 42, 0.09);
  transform: translateY(-1px);
}

.desktop-order-row:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
  box-shadow: 0 0 0 3px rgba(0, 95, 111, 0.18);
}

.desktop-order-col {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.desktop-order-col-beach strong {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-order-label {
  color: #73858a;
  font-size: 12px;
  font-weight: 600;
}

.desktop-order-col strong {
  color: #414d4f;
  font-size: 16px;
  line-height: 1.2;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.desktop-records-count {
  text-align: center;
  margin: 16px 0 0;
  color: #6b7280;
  font-size: 13px;
  font-weight: 600;
}

.order-history-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 12px;
  margin-bottom: 8px;
}

.order-history-back {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  color: #414d4f;
  display: flex;
  align-items: center;
  transition: color 0.2s ease;
}

.order-history-back:hover {
  color: #005f6f;
}

.order-history-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
  flex: 1;
}

.order-history-subtitle {
  margin: 0 0 20px 0;
  font-size: 14px;
  color: #78898c;
  font-weight: 400;
}

.order-history-loading,
.order-history-error,
.order-history-empty {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 16px;
}

.order-history-error p {
  margin: 0;
  color: #d32f2f;
  font-size: 14px;
}

.order-history-empty p {
  margin: 0;
  color: #78898c;
  font-size: 16px;
}

.order-history-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0;
}

.order-card {
  border: 0;
  text-align: left;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(15, 23, 42, 0.05);
  transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}

.order-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.09);
}

.order-card:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.order-card-content {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  min-width: 0;
}

.order-card-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.order-card-beach {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  color: #242b2c;
}

.order-card-city {
  font-size: 12px;
  color: #78898c;
}

.order-card-date {
  font-size: 13px;
  color: #414d4f;
  font-weight: 600;
  white-space: nowrap;
}

.order-card-arrow {
  color: #8a969b;
  flex-shrink: 0;
}

.order-history-count {
  margin: 20px 0 16px 0;
  font-size: 13px;
  color: #9ca3af;
  text-align: center;
  font-weight: 600;
}

.order-detail-modal-overlay {
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 16px;
}

.order-detail-modal-overlay.desktop {
  align-items: center;
}

.order-detail-modal {
  background: #ffffff;
  border-radius: 20px;
  padding: 24px;
  width: 100%;
  max-width: 560px;
  position: relative;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  max-height: 100%;
  overflow-y: auto;
}

.order-detail-modal.desktop {
  border-radius: 20px;
  max-width: 640px;
  width: min(640px, calc(100vw - 64px));
  max-height: calc(100vh - 120px);
  overflow-y: auto;
  padding: 22px 24px 20px;
}

.order-detail-close {
  position: absolute;
  top: 16px;
  right: 16px;
  background: #f3f4f6;
  border: none;
  width: 34px;
  height: 34px;
  border-radius: 999px;
  padding: 0;
  cursor: pointer;
  color: #414d4f;
  display: flex;
  align-items: center;
  justify-content: center;
}

.order-detail-close:focus-visible,
.nav-item:focus-visible,
.desktop-active-link:focus-visible,
.order-history-back:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

.order-detail-title {
  margin: 0 42px 16px 0;
  font-size: 20px;
  font-weight: 700;
  color: #242b2c;
}

.order-detail-content {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px 0;
  border-top: 1px solid #e5e7eb;
  border-bottom: 1px solid #e5e7eb;
}

.order-detail-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
}

.order-detail-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.order-detail-value {
  font-size: 13px;
  font-weight: 700;
  color: #242b2c;
  text-align: right;
}

.order-detail-row-total {
  padding: 10px 0 0;
  margin-top: 4px;
  border-top: 1px dashed #e5e7eb;
}

.order-detail-close-btn {
  width: 100%;
  margin-top: 16px;
}
</style>
