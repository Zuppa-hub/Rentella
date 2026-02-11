<template>
  <div class="screen">
    <header class="topbar">
      <div class="brand">
        <span class="brand-mark">Rentella</span>
      </div>
      <div class="header-actions">
        <button class="pill">
          <span class="dot"></span>
          Rimini
        </button>
        <button v-if="!authenticated" class="avatar" @click="handleLogin">
          Login
        </button>
        <button v-else class="avatar" @click="handleLogout">
          {{ initials }}
        </button>
      </div>
    </header>

    <section class="map-section">
      <div class="map">
        <div class="map-gradient"></div>
        <div class="pin" style="--x: 22%; --y: 28%">1</div>
        <div class="pin" style="--x: 40%; --y: 56%">2</div>
        <div class="pin" style="--x: 52%; --y: 72%">3</div>
        <div class="pin user" style="--x: 18%; --y: 54%">
          <span>{{ initials }}</span>
        </div>
      </div>
      <div class="location-pill">
        <span class="radar"></span>
        Rimini
      </div>
    </section>

    <section class="sheet">
      <div class="sheet-handle">
        <span>Hide</span>
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" />
        </svg>
      </div>
      <div class="search">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none" />
          <path d="M20 20l-4-4" stroke="currentColor" stroke-width="2" fill="none" />
        </svg>
        <input
          v-model="searchTerm"
          placeholder="Search for a Location..."
          aria-label="Search"
        />
      </div>

      <div class="list-header">
        <h2>Available locations</h2>
        <span>{{ filteredLocations.length }} Locations</span>
      </div>

      <div class="location-list">
        <article v-for="(location, index) in filteredLocations" :key="location.id" class="card">
          <div class="badge">{{ index + 1 }}</div>
          <div class="card-body">
            <h3>{{ location.name }}</h3>
            <div class="meta">
              <span class="meta-item">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path
                    d="M12 21s6-5.33 6-10a6 6 0 10-12 0c0 4.67 6 10 6 10z"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  />
                  <circle cx="12" cy="11" r="2.5" fill="currentColor" />
                </svg>
                {{ location.distance }} km
              </span>
              <span class="meta-item">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2" />
                  <path d="M12 7v5l3 3" fill="none" stroke="currentColor" stroke-width="2" />
                </svg>
                {{ location.priceRange }}
              </span>
            </div>
          </div>
          <div class="chevron">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M9 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="2" />
            </svg>
          </div>
        </article>
      </div>
    </section>

    <nav class="bottom-nav">
      <button class="nav-item active">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M4 11l8-6 8 6v7a2 2 0 0 1-2 2h-4v-6h-4v6H6a2 2 0 0 1-2-2z" fill="none" stroke="currentColor" stroke-width="2" />
        </svg>
        Home
      </button>
      <button class="nav-item">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M6 10a6 6 0 0 1 12 0v8H6z" fill="none" stroke="currentColor" stroke-width="2" />
          <path d="M4 18h16" stroke="currentColor" stroke-width="2" />
        </svg>
        Active
      </button>
      <button class="nav-item">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="2" />
          <path d="M12 7v5l3 3" fill="none" stroke="currentColor" stroke-width="2" />
        </svg>
        History
      </button>
      <button class="nav-item">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" fill="none" stroke="currentColor" stroke-width="2" />
          <path
            d="M19.4 15a7.6 7.6 0 0 0 .1-2l2-1.2-2-3.4-2.2.6a7.5 7.5 0 0 0-1.6-1l-.3-2.3H10l-.3 2.3a7.5 7.5 0 0 0-1.6 1l-2.2-.6-2 3.4 2 1.2a7.6 7.6 0 0 0 .1 2l-2 1.2 2 3.4 2.2-.6a7.5 7.5 0 0 0 1.6 1l.3 2.3h4.2l.3-2.3a7.5 7.5 0 0 0 1.6-1l2.2.6 2-3.4z"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linejoin="round"
          />
        </svg>
        Settings
      </button>
    </nav>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { getUser, isAuthenticated, login, logout, updateToken } from './keycloak'

type LocationItem = {
  id: number
  name: string
  distance: number
  priceRange: string
}

const authenticated = ref(false)
const error = ref<string | null>(null)
const user = ref<Record<string, unknown> | undefined>(undefined)
const searchTerm = ref('')
let refreshTimer: number | undefined

const locations = ref<LocationItem[]>([
  { id: 1, name: 'Rimini', distance: 23, priceRange: '37-92EUR' },
  { id: 2, name: 'Pasaro', distance: 23, priceRange: '37-92EUR' },
  { id: 3, name: 'Fano', distance: 23, priceRange: '37-92EUR' },
  { id: 4, name: 'Senigallia', distance: 23, priceRange: '37-92EUR' },
  { id: 5, name: 'Cattolica', distance: 23, priceRange: '37-92EUR' },
])

const filteredLocations = computed(() => {
  if (!searchTerm.value) return locations.value
  const query = searchTerm.value.toLowerCase()
  return locations.value.filter((item) => item.name.toLowerCase().includes(query))
})

const initials = computed(() => {
  const name = String(
    user.value?.name ||
      user.value?.preferred_username ||
      user.value?.email ||
      'JD'
  )
  const parts = name.trim().split(/\s+/)
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase()
  return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
})

const syncState = () => {
  authenticated.value = isAuthenticated()
  user.value = getUser()
}

const startTokenRefresh = () => {
  stopTokenRefresh()
  refreshTimer = window.setInterval(() => {
    if (!isAuthenticated()) {
      stopTokenRefresh()
      return
    }
    updateToken(60).catch((err) => {
      console.error('Token refresh failed', err)
      error.value = 'Token refresh failed'
    })
  }, 30000)
}

const stopTokenRefresh = () => {
  if (refreshTimer) {
    window.clearInterval(refreshTimer)
    refreshTimer = undefined
  }
}

const handleLogin = async () => {
  error.value = null
  try {
    await login()
  } catch (err) {
    error.value = 'Login failed'
    console.error(err)
  }
}

const handleLogout = async () => {
  error.value = null
  try {
    await logout()
  } catch (err) {
    error.value = 'Logout failed'
    console.error(err)
  }
}

onMounted(() => {
  syncState()
  if (isAuthenticated()) {
    startTokenRefresh()
  }
})

onBeforeUnmount(() => {
  stopTokenRefresh()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap');

:global(body) {
  margin: 0;
  font-family: 'Space Grotesk', system-ui, sans-serif;
  background: #0f5f6f;
  color: #0f172a;
}

:global(#app) {
  min-height: 100vh;
  background: #0f5f6f;
}

.screen {
  max-width: 420px;
  margin: 0 auto;
  background: #0f5f6f;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 18px;
  color: #f8fafc;
}

.brand-mark {
  font-weight: 700;
  font-size: 20px;
  letter-spacing: -0.02em;
}

.header-actions {
  display: flex;
  gap: 10px;
  align-items: center;
}

.pill {
  background: rgba(255, 255, 255, 0.85);
  border: none;
  border-radius: 12px;
  padding: 8px 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  color: #243b4a;
}

.pill .dot {
  width: 8px;
  height: 8px;
  background: #0f5f6f;
  border-radius: 999px;
  display: inline-block;
}

.avatar {
  background: #ffffff;
  border: none;
  border-radius: 999px;
  padding: 8px 12px;
  font-weight: 700;
  color: #0f5f6f;
  min-width: 44px;
  text-align: center;
}

.map-section {
  position: relative;
  padding: 0 12px 12px;
}

.map {
  height: 204px;
  border-radius: 18px;
  background: linear-gradient(135deg, #cce9e6, #9fd0d6 45%, #84b9c9);
  position: relative;
  overflow: hidden;
}

.map-gradient {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.8), transparent 50%),
    radial-gradient(circle at 75% 60%, rgba(18, 120, 136, 0.35), transparent 45%);
  mix-blend-mode: screen;
}

.pin {
  position: absolute;
  left: var(--x);
  top: var(--y);
  transform: translate(-50%, -50%);
  width: 36px;
  height: 36px;
  background: #1f2937;
  color: #fff;
  border-radius: 999px 999px 999px 4px;
  display: grid;
  place-items: center;
  font-weight: 600;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.3);
}

.pin.user {
  background: #ffae00;
  color: #111827;
  border-radius: 999px;
  width: 44px;
  height: 44px;
}

.location-pill {
  position: absolute;
  right: 18px;
  top: 18px;
  background: rgba(255, 255, 255, 0.9);
  padding: 8px 12px;
  border-radius: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 6px;
  color: #243b4a;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.12);
}

.radar {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 2px solid #0f5f6f;
  position: relative;
}

.radar::after {
  content: '';
  position: absolute;
  inset: 2px;
  background: #0f5f6f;
  border-radius: 50%;
}

.sheet {
  background: #f8fafc;
  margin-top: -24px;
  border-radius: 32px 32px 0 0;
  padding: 18px 16px 100px;
  box-shadow: 0 -10px 24px rgba(15, 23, 42, 0.12);
  position: relative;
  z-index: 2;
}

.sheet-handle {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  color: #5f6b7a;
  font-weight: 600;
  margin-bottom: 16px;
}

.sheet-handle svg {
  width: 20px;
  height: 20px;
}

.search {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #eef2f6;
  border-radius: 14px;
  padding: 10px 12px;
  color: #7b8794;
  margin-bottom: 18px;
}

.search svg {
  width: 20px;
  height: 20px;
}

.search input {
  border: none;
  background: transparent;
  width: 100%;
  font-size: 14px;
  outline: none;
  font-family: inherit;
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
}

.list-header h2 {
  font-size: 18px;
  margin: 0;
}

.list-header span {
  color: #7b8794;
  font-size: 13px;
}

.location-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.card {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 12px;
  background: #ffffff;
  border-radius: 16px;
  padding: 12px 14px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.04);
}

.badge {
  background: #1f2937;
  color: #fff;
  width: 32px;
  height: 32px;
  border-radius: 999px 999px 999px 6px;
  display: grid;
  place-items: center;
  font-weight: 600;
}

.card-body h3 {
  margin: 0;
  font-size: 16px;
}

.meta {
  display: flex;
  gap: 16px;
  margin-top: 6px;
  color: #6b7280;
  font-size: 13px;
}

.meta-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.meta-item svg {
  width: 16px;
  height: 16px;
}

.chevron svg {
  width: 20px;
  height: 20px;
  color: #94a3b8;
}

.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  max-width: 420px;
  width: 100%;
  background: #0f5f6f;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  padding: 12px 6px 18px;
  gap: 6px;
  color: #d9f0f2;
}

.nav-item {
  background: transparent;
  border: none;
  color: inherit;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
}

.nav-item svg {
  width: 22px;
  height: 22px;
}

.nav-item.active {
  color: #ffffff;
}

.error {
  position: fixed;
  bottom: 88px;
  left: 50%;
  transform: translateX(-50%);
  background: #fee2e2;
  color: #b91c1c;
  padding: 8px 16px;
  border-radius: 999px;
  font-weight: 600;
  box-shadow: 0 6px 16px rgba(185, 28, 28, 0.2);
}

@media (min-width: 768px) {
  .screen {
    max-width: 680px;
  }

  .bottom-nav {
    max-width: 680px;
  }
}
</style>
