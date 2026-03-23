<template>
  <nav class="bottom-nav">
    <button class="nav-item" :class="{ active: activeTab === 'home' }" @click="handleNavigation('home')">
      <img :src="icons.home" alt="" />
      Home
    </button>
    <button class="nav-item" :class="{ active: activeTab === 'active' }" @click="handleNavigation('active')">
      <img :src="icons.active" alt="" />
      Active bookings
    </button>
    <button class="nav-item" :class="{ active: activeTab === 'history' }" @click="handleNavigation('history')">
      <img :src="icons.history" alt="" />
      Order history
    </button>
    <button class="nav-item" :class="{ active: activeTab === 'settings' }" @click="handleNavigation('settings')">
      <img :src="icons.settings" alt="" />
      Settings
    </button>
  </nav>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import iconHistory from '../assets/icons/History.svg'
import iconHome from '../assets/icons/Home.svg'
import iconSettings from '../assets/icons/Settings.svg'
import iconActive from '../assets/icons/Active.svg'

const activeTab = ref('home')

const emit = defineEmits<{
  navigate: [tab: string]
}>()

const handleNavigation = (tab: string) => {
  activeTab.value = tab
  emit('navigate', tab)
}
const icons = {
  history: iconHistory,
  home: iconHome,
  settings: iconSettings,
  active: iconActive,
}
</script>

<style scoped>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #0b5f6f;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  padding: 10px clamp(8px, 2vw, 16px) calc(20px + env(safe-area-inset-bottom));
  gap: 6px;
  color: #d2eef1;
  min-height: 80px;
  box-sizing: border-box;
  z-index: 101;
}

.nav-item {
  background: transparent;
  border: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
}

.nav-item img {
  width: 22px;
  height: 22px;
}

.nav-item.active {
  color: #ffffff;
}
</style>
