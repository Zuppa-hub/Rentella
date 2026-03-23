<template>
  <nav class="navbar">
    <div class="navbar-container">
      <div class="logo-section">
        <img :src="icons.logo" :alt="t('desktop.brand.alt')" class="logo" />
      </div>
      <div class="nav-items">
        <button
          class="nav-item"
          :class="{ active: activeTab === 'home' }"
          type="button"
          @click="emit('navigate', 'home')"
        >
          <img :src="icons.home" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.home') }}</span>
        </button>
        <button
          class="nav-item"
          :class="{ active: activeTab === 'active' }"
          type="button"
          @click="emit('navigate', 'active')"
        >
          <img :src="icons.active" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.active') }}</span>
        </button>
        <button
          class="nav-item"
          :class="{ active: activeTab === 'history' }"
          type="button"
          @click="emit('navigate', 'history')"
        >
          <img :src="icons.history" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.history') }}</span>
        </button>
        <button
          class="nav-item"
          :class="{ active: activeTab === 'settings' }"
          type="button"
          @click="emit('navigate', 'settings')"
        >
          <img :src="icons.settings" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.settings') }}</span>
        </button>
      </div>
      <div class="profile-section">
        <div class="profile-avatar">{{ initials }}</div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import logoDark from '../../assets/LogoDark.svg'
import homeIcon from '../../assets/icons/Home.svg'
import activeIcon from '../../assets/icons/Active.svg'
import historyIcon from '../../assets/icons/History.svg'
import settingsIcon from '../../assets/icons/Settings.svg'

defineProps<{
  initials: string
  activeTab?: string
}>()

const emit = defineEmits<{
  (event: 'navigate', tab: string): void
}>()

const { t } = useI18n()

const icons = {
  logo: logoDark,
  home: homeIcon,
  active: activeIcon,
  history: historyIcon,
  settings: settingsIcon,
}
</script>

<style scoped>
/* Navbar Styles */
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
  border: 0;
  background: transparent;
  color: #d2eef1;
  font-size: 11px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  transition: opacity 0.3s ease;
}

.nav-item:hover {
  opacity: 0.8;
}

.nav-item.active {
  color: #ffffff;
}

.nav-icon {
  width: 22px;
  height: 22px;
  padding: 0;
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
  background: #1f2937;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
}
</style>
