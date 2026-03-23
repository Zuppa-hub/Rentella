<template>
  <div class="settings-page" :class="{ desktop: isDesktop }">
    <template v-if="isDesktop">
        <DesktopHomeNavbar :initials="initials || 'JD'" active-tab="settings" @navigate="emit('navigate', $event)" />

      <div class="desktop-layout" role="main" :aria-label="t('desktop.settings.title')">
        <section class="desktop-main" :aria-label="t('desktop.settings.manageAccount')">
          <h2 class="desktop-main-title">{{ t('desktop.settings.title') }}</h2>
          <AccountDetailsCard :full-name="fullName" :email="email" :username="username" @logout="emit('logout')" />
        </section>
      </div>
    </template>

    <template v-else>
      <div class="settings-header">
        <button class="settings-back" @click="emit('back')" :aria-label="t('desktop.settings.goBack')">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        <h1 class="settings-title">{{ t('desktop.settings.title') }}</h1>
      </div>

      <p class="settings-subtitle">{{ t('desktop.settings.manageAccount') }}</p>

      <section class="settings-mobile-section" :aria-label="t('desktop.settings.account')">
        <h2>{{ t('desktop.settings.account') }}</h2>
        <AccountDetailsCard :full-name="fullName" :email="email" :username="username" @logout="emit('logout')" />
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import DesktopHomeNavbar from './home/DesktopHomeNavbar.vue'
import AccountDetailsCard from './settings/AccountDetailsCard.vue'

const { t } = useI18n()

defineProps<{
  isDesktop?: boolean
  initials?: string
  fullName?: string
  email?: string
  username?: string
}>()

const emit = defineEmits<{
  back: []
  navigate: [tab: string]
  logout: []
}>()
</script>

<style scoped>
.settings-page {
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

.settings-page.desktop {
  position: relative;
  inset: auto;
  padding: 0;
  height: 100vh;
  background: #f3f4f5;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.desktop-layout {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.desktop-main {
  padding: 24px 24px 18px;
  min-width: 0;
  flex: 1;
  min-height: 0;
  overflow-y: auto;
}

.desktop-main-title {
  margin: 0 0 18px;
  font-size: 20px;
  line-height: 1.2;
  font-weight: 600;
  color: #414d4f;
}

.settings-header {
  display: flex;
  align-items: center;
  gap: 12px;
  position: sticky;
  top: 0;
  z-index: 2;
  padding-bottom: 12px;
  background: #fff;
}

.settings-back {
  width: auto;
  height: auto;
  border-radius: 0;
  border: none;
  background: transparent;
  display: flex;
  align-items: center;
  padding: 8px;
  color: #0b5f6f;
  cursor: pointer;
}

.settings-back:hover {
  color: #005f6f;
}

.settings-title {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
}

.settings-subtitle {
  margin: 0 0 16px;
  font-size: 14px;
  color: #78898c;
}

.settings-mobile-section {
  margin-bottom: 18px;
}

.settings-mobile-section h2 {
  margin: 0;
  font-size: 16px;
  color: #334245;
}

@media (min-width: 1024px) {
  .settings-logout-btn {
    width: auto;
    align-self: flex-end;
  }
}

.settings-back:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

@media (max-width: 1023px) {
  .settings-page.desktop {
    display: none;
  }
}
</style>