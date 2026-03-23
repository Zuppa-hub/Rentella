<template>
  <div class="settings-page" :class="{ desktop: isDesktop }">
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
            <button class="nav-item" type="button" @click="emit('navigate', 'history')">
              <img :src="icons.history" alt="" class="nav-icon" />
              <span>History</span>
            </button>
            <button class="nav-item active" type="button" aria-current="page">
              <img :src="icons.settings" alt="" class="nav-icon" />
              <span>Settings</span>
            </button>
          </div>
          <div class="profile-section">
            <div class="profile-avatar">{{ initials || 'JD' }}</div>
          </div>
        </div>
      </nav>

      <div class="desktop-layout" role="main" aria-label="Settings desktop layout">
        <section class="desktop-main" aria-label="Account settings">
          <h2 class="desktop-main-title">Settings</h2>

          <div class="settings-card">
            <div class="settings-card-row">
              <span>Full Name</span>
              <strong>{{ fullName || '-' }}</strong>
            </div>
            <div class="settings-card-row">
              <span>Email</span>
              <strong>{{ email || '-' }}</strong>
            </div>
            <div class="settings-card-row">
              <span>Username</span>
              <strong>{{ username || '-' }}</strong>
            </div>
            <button type="button" class="settings-logout-btn" @click="emit('logout')">Log out</button>
          </div>
        </section>
      </div>
    </template>

    <template v-else>
      <div class="settings-header">
        <button class="settings-back" @click="emit('back')" aria-label="Go back">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"></polyline>
          </svg>
        </button>
        <h1 class="settings-title">Settings</h1>
      </div>

      <p class="settings-subtitle">Manage your account</p>

      <section class="settings-mobile-section" aria-label="Account details">
        <h2>Account</h2>
        <div class="settings-mobile-card">
          <div class="settings-mobile-row">
            <span>Full Name</span>
            <strong>{{ fullName || '-' }}</strong>
          </div>
          <div class="settings-mobile-row">
            <span>Email</span>
            <strong>{{ email || '-' }}</strong>
          </div>
          <div class="settings-mobile-row">
            <span>Username</span>
            <strong>{{ username || '-' }}</strong>
          </div>
          <button type="button" class="settings-logout-btn" @click="emit('logout')">Log out</button>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup lang="ts">
import logoDark from '../assets/LogoDark.svg'
import homeIcon from '../assets/icons/Home.svg'
import activeIcon from '../assets/icons/Active.svg'
import historyIcon from '../assets/icons/History.svg'
import settingsIcon from '../assets/icons/Settings.svg'

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

const icons = {
  logo: logoDark,
  home: homeIcon,
  active: activeIcon,
  history: historyIcon,
  settings: settingsIcon,
}
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
}

.navbar {
  height: 72px;
  background: #005f6f;
  box-shadow: 0 -4px 8px rgba(85, 85, 85, 0.08);
  display: flex;
  align-items: center;
  padding: 0 32px;
}

.navbar-container {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 16px;
}

.logo { height: 32px; width: auto; }
.nav-items { display: flex; margin-left: auto; margin-right: 12px; }

.nav-item {
  display: flex;
  align-items: center;
  gap: 4px;
  padding: 0 16px;
  height: 100%;
  border: 0;
  background: transparent;
  color: #d2eef1;
  font-size: 11px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  transition: opacity 0.3s ease;
}

.nav-item:hover {
  opacity: 0.8;
}

.nav-item.active { color: #fff; }
.nav-icon { width: 22px; height: 22px; }

.profile-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f0f4f6;
  color: #1f2937;
  display: grid;
  place-items: center;
  font-size: 14px;
  font-weight: 600;
}

.desktop-layout {
  flex: 1;
  min-height: 0;
  display: block;
  overflow: hidden;
}

.desktop-main {
  padding: 24px 24px 18px;
  min-width: 0;
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

.settings-card,
.settings-mobile-card {
  margin-top: 14px;
  border: 1px solid #d7dee2;
  border-radius: 16px;
  background: #ffffff;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.settings-card-row,
.settings-mobile-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  font-size: 13px;
  color: #73858a;
}

.settings-card-row strong,
.settings-mobile-row strong {
  color: #2f3b3e;
}

.settings-logout-btn {
  margin-top: 6px;
  align-self: flex-start;
  width: 100%;
  border: none;
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 13px;
  font-weight: 600;
  background: #0b5f6f;
  color: #ffffff;
  cursor: pointer;
}

.settings-logout-btn:hover {
  opacity: 0.92;
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

.settings-back:focus-visible,
.settings-logout-btn:focus-visible,
.nav-item:focus-visible {
  outline: 2px solid #005f6f;
  outline-offset: 2px;
}

@media (max-width: 1023px) {
  .settings-page.desktop {
    display: none;
  }
}
</style>