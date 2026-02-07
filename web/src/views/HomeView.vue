<template>
  <div class="flex flex-col h-screen">
    <!-- Top navigation -->
    <TopBar />

    <!-- Main content -->
    <div class="flex flex-1 flex-col md:flex-row overflow-hidden">
      <!-- Mobile map (2/5 height) -->
      <div class="md:hidden flex-none h-2/5 -ml-8 z-0">
        <Map v-if="locations.length" :locations="locations" />
        <div v-else class="flex items-center justify-center h-full bg-gray-100">
          <span class="text-gray-500">Loading...</span>
        </div>
      </div>

      <!-- Sidebar with locations list -->
      <Sidebar
        :locations="locations"
        :loading="loading"
        :total-count="locations.length"
        @select="handleLocationSelect"
      />

      <!-- Desktop map (2/3 width) -->
      <div class="hidden md:block flex-1 bg-gray-100 -ml-8 z-0">
        <Map v-if="locations.length" :locations="locations" />
        <div v-else class="flex items-center justify-center h-full">
          <span class="text-gray-500">Loading...</span>
        </div>
      </div>
    </div>

    <!-- Bottom navigation -->
    <NavBar />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBeachStore } from '@/stores/useBeachStore'
import TopBar from '@/components/TopBar.vue'
import NavBar from '@/components/NavBar.vue'
import Map from '@/components/Map.vue'
import Sidebar from '@/components/Sidebar.vue'

// Store
const beachStore = useBeachStore()
const router = useRouter()

// State from store
const locations = computed(() => beachStore.locations)
const loading = computed(() => beachStore.loading)

// Load locations on mount
onMounted(async () => {
  if (locations.value.length === 0) {
    await beachStore.fetchLocations()
  }
})

// Handler
async function handleLocationSelect(locationId: number) {
  await router.push({
    name: 'BeachSelection',
    params: { id: locationId },
  })
}
</script>

<style scoped>
.map-container {
  flex: 1;
  overflow: hidden;
}
</script>


