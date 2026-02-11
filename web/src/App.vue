<template>
  <div class="page">
    <header class="header">
      <div>
        <h1>Rentella Auth</h1>
        <p class="subtitle">Keycloak base app</p>
      </div>
      <div class="actions">
        <button v-if="!authenticated" class="btn" @click="handleLogin">
          Login
        </button>
        <button v-else class="btn secondary" @click="handleLogout">
          Logout
        </button>
      </div>
    </header>

    <section class="card">
      <div class="row">
        <span class="label">Status</span>
        <span class="value" :class="authenticated ? 'ok' : 'warn'">
          {{ authenticated ? 'Authenticated' : 'Not authenticated' }}
        </span>
      </div>
      <div class="row">
        <span class="label">Realm</span>
        <span class="value">{{ env.realm }}</span>
      </div>
      <div class="row">
        <span class="label">Client</span>
        <span class="value">{{ env.clientId }}</span>
      </div>
      <div class="row">
        <span class="label">Redirect</span>
        <span class="value">{{ env.redirectUri }}</span>
      </div>
    </section>

    <section class="card" v-if="authenticated">
      <h2>User</h2>
      <pre>{{ user }}</pre>
    </section>

    <section class="card" v-if="authenticated">
      <h2>Access Token</h2>
      <pre>{{ token }}</pre>
    </section>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import {
  getToken,
  getUser,
  isAuthenticated,
  login,
  logout,
  updateToken,
} from './keycloak'

const authenticated = ref(false)
const token = ref('')
const user = ref<Record<string, unknown> | undefined>(undefined)
const error = ref<string | null>(null)
let refreshTimer: number | undefined

const env = computed(() => ({
  realm: import.meta.env.VITE_KEYCLOAK_REALM,
  clientId: import.meta.env.VITE_KEYCLOAK_CLIENT_ID,
  redirectUri:
    import.meta.env.VITE_KEYCLOAK_REDIRECT_URI || `${window.location.origin}/`,
}))

const syncState = () => {
  authenticated.value = isAuthenticated()
  token.value = getToken() || ''
  user.value = getUser()
}

const startTokenRefresh = () => {
  stopTokenRefresh()
  refreshTimer = window.setInterval(() => {
    if (!isAuthenticated()) {
      stopTokenRefresh()
      return
    }
    updateToken(60)
      .then(syncState)
      .catch((err) => {
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
:global(body) {
  margin: 0;
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  background: #f4f5f7;
  color: #1f2937;
}

.page {
  max-width: 960px;
  margin: 0 auto;
  padding: 48px 24px 72px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.subtitle {
  margin: 6px 0 0;
  color: #6b7280;
}

.actions {
  display: flex;
  gap: 12px;
}

.btn {
  border: none;
  background: #111827;
  color: #fff;
  padding: 10px 18px;
  border-radius: 8px;
  cursor: pointer;
}

.btn.secondary {
  background: #f97316;
}

.card {
  background: #fff;
  border-radius: 14px;
  padding: 20px 24px;
  box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
}

.row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 0;
  border-bottom: 1px solid #e5e7eb;
}

.row:last-child {
  border-bottom: none;
}

.label {
  color: #6b7280;
}

.value {
  font-weight: 600;
}

.value.ok {
  color: #16a34a;
}

.value.warn {
  color: #dc2626;
}

pre {
  white-space: pre-wrap;
  word-break: break-word;
  background: #0f172a;
  color: #e2e8f0;
  padding: 16px;
  border-radius: 10px;
  font-size: 12px;
}

.error {
  color: #dc2626;
  font-weight: 600;
}
</style>
