import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types'
import keycloakService from '@/KeycloakService'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const isAuthenticated = ref(false)
  const loading = ref(false)
  const error = ref<string | null>(null)

  const hasToken = computed(() => {
    return keycloakService.IsLoggedIn()
  })

  async function initAuth() {
    loading.value = true
    try {
      if (keycloakService.IsLoggedIn()) {
        isAuthenticated.value = true
        // Get user from Keycloak token
        const userInfo = keycloakService.GetUserProfile()
        user.value = {
          id: 1, // Would come from backend
          email: userInfo?.email || '',
          name: userInfo?.name || '',
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
        }
      }
    } catch (err) {
      error.value = 'Failed to initialize auth'
      console.error(err)
    } finally {
      loading.value = false
    }
  }

  function login() {
    keycloakService.CallLogin()
  }

  function logout() {
    keycloakService.CallLogOut()
    user.value = null
    isAuthenticated.value = false
  }

  function clearError() {
    error.value = null
  }

  return {
    user,
    isAuthenticated,
    loading,
    error,
    hasToken,
    initAuth,
    login,
    logout,
    clearError,
  }
})
