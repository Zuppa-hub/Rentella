<template>
  <nav class="navbar">
    <div class="navbar-container">
      <div class="logo-section">
        <img :src="logoDark" :alt="t('desktop.brand.alt')" class="logo" />
      </div>
      <div class="nav-items">
        <button class="nav-item" type="button" @click="emit('navigate', 'home')">
          <img :src="homeIcon" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.home') }}</span>
        </button>
        <button class="nav-item" type="button" @click="emit('navigate', 'active')">
          <img :src="activeIcon" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.active') }}</span>
        </button>
        <button class="nav-item active" type="button" aria-current="page">
          <img :src="historyIcon" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.history') }}</span>
        </button>
        <button class="nav-item" type="button" @click="emit('navigate', 'settings')">
          <img :src="settingsIcon" alt="" class="nav-icon" />
          <span>{{ t('desktop.nav.settings') }}</span>
        </button>
      </div>
      <div class="profile-section">
        <div class="profile-avatar">{{ initials || 'JD' }}</div>
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
  initials?: string
}>()

const emit = defineEmits<{
  navigate: [tab: string]
}>()

const { t } = useI18n()
</script>

<style scoped>
.navbar {
  height: 72px;
  background: var(--color-primary, #005f6f);
  box-shadow: 0 -4px 8px rgba(85, 85, 85, 0.08);
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

.logo {
  height: 32px;
  width: auto;
}

.nav-items {
  display: flex;
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

.nav-item:focus-visible {
  outline: 2px solid var(--color-primary, #005f6f);
  outline-offset: 2px;
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
</style>
