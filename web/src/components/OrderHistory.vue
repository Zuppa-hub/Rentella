<template>
  <div class="order-history">
    <!-- Header -->
    <div class="order-history-header">
      <button class="order-history-back" @click="handleBack">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <h1 class="order-history-title">Order History</h1>
    </div>

    <p class="order-history-subtitle">See all the past Purchases</p>

    <!-- Orders List -->
    <div class="order-history-list">
      <div
        v-for="order in allOrders"
        :key="order.id"
        class="order-card"
        @click="selectOrder(order)"
      >
        <div class="order-card-content">
          <div class="order-card-main">
            <h3 class="order-card-beach">{{ order.beachName }}</h3>
            <span class="order-card-city">{{ order.location }}</span>
          </div>
          <span class="order-card-date">{{ formatDate(order.checkInDate) }}</span>
        </div>
        <svg class="order-card-arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </div>
    </div>

    <!-- Records Count -->
    <p class="order-history-count">{{ allOrders.length }} Records</p>

    <!-- Active Orders Section -->
    <div class="order-history-active">
      <p class="order-history-active-question">Looking for Active Orders?</p>
      <button class="order-history-active-btn" @click="showActive = true">Active</button>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="order-detail-modal-overlay" @click="selectedOrder = null">
      <div class="order-detail-modal" @click.stop>
        <button class="order-detail-close" @click="selectedOrder = null">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <h3 class="order-detail-title">{{ selectedOrder.beachName }}</h3>
        <div class="order-detail-content">
          <div class="order-detail-row">
            <span class="order-detail-label">Order ID:</span>
            <span class="order-detail-value">{{ selectedOrder.id }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Location:</span>
            <span class="order-detail-value">{{ selectedOrder.location }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Zone:</span>
            <span class="order-detail-value">{{ selectedOrder.zoneName }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Check-in:</span>
            <span class="order-detail-value">{{ selectedOrder.checkInDate }}</span>
          </div>
          <div class="order-detail-row">
            <span class="order-detail-label">Check-out:</span>
            <span class="order-detail-value">{{ selectedOrder.checkOutDate }}</span>
          </div>
          <div class="order-detail-row order-detail-row-total">
            <span class="order-detail-label">Total Price:</span>
            <span class="order-detail-value">{{ selectedOrder.totalPrice }}</span>
          </div>
        </div>
        <button class="order-detail-close-btn rt-btn rt-btn-primary" @click="selectedOrder = null">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Order {
  id: string
  beachName: string
  zoneName: string
  location: string
  checkInDate: string
  checkOutDate: string
  totalPrice: string
  createdAt: number
  status: 'completed' | 'active' | 'cancelled'
}

const emit = defineEmits<{
  back: []
}>()

const selectedOrder = ref<Order | null>(null)
const showActive = ref(false)

// Load orders from localStorage (simulated data)
const allOrders = computed(() => {
  const stored = localStorage.getItem('rentella_orders')
  const orders: Order[] = stored ? JSON.parse(stored) : [
    {
      id: 'ORD-20230804-1234',
      beachName: 'Tortuga Beach',
      zoneName: 'Zone A',
      location: 'Rimini',
      checkInDate: '2023-08-04',
      checkOutDate: '2023-08-06',
      totalPrice: '150 €',
      createdAt: Date.now() - 30 * 24 * 60 * 60 * 1000,
      status: 'completed',
    },
    {
      id: 'ORD-20230805-5678',
      beachName: 'Tortuga Beach',
      zoneName: 'Zone B',
      location: 'Rimini',
      checkInDate: '2023-08-05',
      checkOutDate: '2023-08-07',
      totalPrice: '180 €',
      createdAt: Date.now() - 25 * 24 * 60 * 60 * 1000,
      status: 'completed',
    },
    {
      id: 'ORD-20230810-9012',
      beachName: 'Tortuga Beach',
      zoneName: 'Zone C',
      location: 'Rimini',
      checkInDate: '2023-08-10',
      checkOutDate: '2023-08-12',
      totalPrice: '200 €',
      createdAt: Date.now() - 20 * 24 * 60 * 60 * 1000,
      status: 'completed',
    },
    {
      id: 'ORD-20230815-3456',
      beachName: 'Tortuga Beach',
      zoneName: 'Zone A',
      location: 'Rimini',
      checkInDate: '2023-08-15',
      checkOutDate: '2023-08-18',
      totalPrice: '225 €',
      createdAt: Date.now() - 10 * 24 * 60 * 60 * 1000,
      status: 'completed',
    },
  ]
  return orders.sort((a, b) => b.createdAt - a.createdAt)
})

const formatDate = (dateStr: string): string => {
  const [year, month, day] = dateStr.split('-')
  return `${day}.${month}.${year}`
}

const selectOrder = (order: Order) => {
  selectedOrder.value = order
}

const handleBack = () => {
  emit('back')
}
</script>

<style scoped>
.order-history {
  width: 100%;
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Inter', sans-serif;
  border-radius: 32px 32px 0 0;
  padding: 20px clamp(12px, 4vw, 32px) calc(100px + env(safe-area-inset-bottom));
  box-shadow: 0 -2px 8px rgba(85, 85, 102, 0.18);
  min-height: calc(100vh - 80px - (64px + 80px - 67px));
  max-height: calc(100vh - 80px - (64px + 80px - 67px));
  box-sizing: border-box;
  position: relative;
  z-index: 99;
  overflow: hidden;
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

.order-history-list {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0;
}

.order-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px;
  background: #f8fafb;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.order-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

.order-card:active {
  transform: scale(0.98);
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
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.order-card-city {
  font-size: 12px;
  color: #78898c;
  font-weight: 400;
}

.order-card-date {
  font-size: 13px;
  color: #414d4f;
  font-weight: 600;
  white-space: nowrap;
}

.order-card-arrow {
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease;
}

.order-card:hover .order-card-arrow {
  color: #005f6f;
}

.order-history-count {
  margin: 20px 0 16px 0;
  font-size: 13px;
  color: #9ca3af;
  text-align: center;
  font-weight: 600;
}

.order-history-active {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px 0;
  border-top: 1px solid #e5e7eb;
}

.order-history-active-question {
  margin: 0;
  font-size: 14px;
  color: #414d4f;
  font-weight: 600;
}

.order-history-active-btn {
  background: #005f6f;
  color: white;
  border: none;
  padding: 10px 24px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s ease;
}

.order-history-active-btn:hover {
  background: #003d47;
}

.order-detail-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: flex-end;
  z-index: 2000;
  padding: 20px;
}

.order-detail-modal {
  background: #ffffff;
  border-radius: 24px 24px 0 0;
  padding: 24px;
  width: 100%;
  max-width: 600px;
  position: relative;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.order-detail-close {
  position: absolute;
  top: 16px;
  right: 16px;
  background: none;
  border: none;
  padding: 8px;
  cursor: pointer;
  color: #414d4f;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s ease;
}

.order-detail-close:hover {
  color: #005f6f;
}

.order-detail-title {
  margin: 0 0 16px 0;
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
  padding: 8px 0;
  font-size: 16px;
  font-weight: 700;
}

.order-detail-close-btn {
  width: 100%;
  margin-top: 16px;
}

@media (max-width: 640px) {
  .order-history {
    padding: 16px clamp(12px, 4vw, 20px) calc(100px + env(safe-area-inset-bottom));
  }

  .order-history-title {
    font-size: 24px;
  }

  .order-detail-modal {
    max-width: none;
  }
}

@media (min-width: 1024px) {
  .order-history {
    width: 500px;
    min-width: 500px;
    max-width: 500px;
    flex: 0 0 500px;
    background: #fafafc;
    border-radius: 0 32px 32px 0;
    box-shadow: 8px 0px 8px rgba(136, 136, 136, 0.16);
    padding: 32px 0;
    margin: 0;
    margin-top: 0;
    overflow: hidden;
    z-index: 2;
    order: 1;
    height: auto;
    min-height: 0;
    max-height: none;
  }

  .order-history-header {
    padding: 0 16px 12px 16px;
  }

  .order-history-subtitle {
    padding: 0 16px;
  }

  .order-history-list {
    padding: 0 16px;
  }

  .order-history-active {
    padding: 16px;
  }
}
</style>
