<template>
  <div class="h-screen flex flex-col">
    <TopBar />
    <div class="flex flex-1 overflow-hidden">
      <div class="hidden md:flex md:w-1/4 flex-col border-r border-gray-200">
        <div class="p-4">
          <h2 class="text-xl font-bold">Order History</h2>
          <p class="text-sm text-gray-500">{{ orders.length }} total orders</p>
        </div>
      </div>
      <div class="flex-1 w-full md:w-3/4">
        <Sidebar
          :locations="orders"
          :loading="loading"
          :total-count="orders.length"
          title="Your Order History"
          @select="handleOrderSelect"
        />
      </div>
    </div>
    <NavBar />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from '@/stores/useOrderStore'
import TopBar from '@/components/TopBar.vue'
import NavBar from '@/components/NavBar.vue'
import Sidebar from '@/components/Sidebar.vue'

const orderStore = useOrderStore()
const router = useRouter()

const orders = computed(() => orderStore.orders)
const loading = computed(() => orderStore.loading)

onMounted(async () => {
  await orderStore.fetchOrders()
})

function handleOrderSelect(orderId: number) {
  router.push({
    name: 'OrderDetail',
    params: { id: orderId },
  })
}
</script>

<style scoped>
.map-container {
  flex: 1;
  overflow: hidden;
}
</style>
