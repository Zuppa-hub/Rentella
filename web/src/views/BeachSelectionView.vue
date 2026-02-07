<template>
  <div class="flex flex-col h-screen">
    <TopBar />
    <div class="flex flex-1 flex-col md:flex-row overflow-hidden">
      <div class="md:hidden flex-none h-2/5 -ml-8 z-0">
        <Map v-if="beaches.length" :locations="beaches" />
        <div v-else class="flex items-center justify-center h-full bg-gray-100">
          <span class="text-gray-500">No beaches found</span>
        </div>
      </div>
      <Sidebar
        :locations="beaches"
        :loading="loading"
        :total-count="beaches.length"
        @select="handleBeachSelect"
      />
      <div class="hidden md:block flex-1 bg-gray-100 -ml-8 z-0">
        <Map v-if="beaches.length" :locations="beaches" />
        <div v-else class="flex items-center justify-center h-full">
          <span class="text-gray-500">No beaches found</span>
        </div>
      </div>
    </div>
    <NavBar />
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useBeachStore } from '@/stores/useBeachStore'
import TopBar from '@/components/TopBar.vue'
import NavBar from '@/components/NavBar.vue'
import Map from '@/components/Map.vue'
import Sidebar from '@/components/Sidebar.vue'

const beachStore = useBeachStore()
const route = useRoute()

const locationId = computed(() => Number(route.params.id))
const beaches = computed(() => beachStore.beaches)
const loading = computed(() => beachStore.loading)

onMounted(async () => {
  if (locationId.value) {
    await beachStore.searchBeaches(`location:${locationId.value}`)
  }
})

function handleBeachSelect(beachId: number) {
  console.log('Selected beach:', beachId)
}
</script>

<style scoped>
.map-container {
  flex: 1;
  overflow: hidden;
}
</style>

</script>