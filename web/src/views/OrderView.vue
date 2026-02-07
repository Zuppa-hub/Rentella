<template>
  <div class="h-screen flex flex-col">
    <TopBar />
    <div class="flex flex-1 overflow-hidden">
      <div class="hidden md:flex md:w-1/4 flex-col border-r border-gray-200">
        <div class="p-4 border-b border-gray-200">
          <h2 class="text-xl font-bold">Your Orders</h2>
          <p class="text-sm text-gray-500">{{ activeOrders.length }} active</p>
        </div>
        <div v-if="favoriteBeach" class="p-4 border-b border-gray-200">
          <p class="text-sm text-gray-500">Favorite Location</p>
          <p class="font-semibold">{{ favoriteBeach }}</p>
        </div>
      </div>
      <div class="flex-1 w-full md:w-3/4">
        <Sidebar
          :locations="orders"
          :loading="loading"
          :total-count="orders.length"
          title="Your Orders"
          @select="handleOrderSelect"
        />
      </div>
    </div>
    <NavBar />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useOrderStore } from '@/stores/useOrderStore'
import TopBar from '@/components/TopBar.vue'
import NavBar from '@/components/NavBar.vue'
import Sidebar from '@/components/Sidebar.vue'

const orderStore = useOrderStore()
const router = useRouter()
const favoriteBeach = ref<string | null>(null)

const orders = computed(() => orderStore.orders)
const activeOrders = computed(() => orderStore.activeOrders)
const loading = computed(() => orderStore.loading)

onMounted(async () => {
  await orderStore.fetchOrders()
  if (activeOrders.value.length > 0) {
    favoriteBeach.value = 'Most Visited Location'
  }
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
