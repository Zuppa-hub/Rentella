<template>
  <div class="flex flex-row h-screen">
    <div class="md:w-2/5 w-full bg-white dark:bg-gray-950 overflow-y-auto">
      <div class="flex flex-col justify-center min-h-screen p-8">
        <div class="mb-8 h-16 bg-LogoLight dark:bg-LogoDark bg-no-repeat bg-contain"></div>

        <hr class="border-gray-300 dark:border-gray-600 mb-8" />

        <h1 class="text-3xl font-bold text-center mb-4 text-zinc-800 dark:text-zinc-300">
          {{ isLogin ? 'Welcome to Rentella' : 'Register' }}
        </h1>
        <p class="text-lg text-center mb-8 text-gray-900 dark:text-white">
          {{
            isLogin
              ? 'The easiest way to rent an umbrella on the beach.'
              : 'Create an Account for FREE'
          }}
        </p>

        <form @submit.prevent="isLogin ? handleLogin() : handleRegistration()" class="space-y-6">
          <div v-if="!isLogin">
            <label for="name" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              First Name
            </label>
            <input
              v-model="form.name"
              type="text"
              id="name"
              placeholder="John"
              required
              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="!isLogin">
            <label for="surname" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              Last Name
            </label>
            <input
              v-model="form.surname"
              type="text"
              id="surname"
              placeholder="Doe"
              required
              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              Email
            </label>
            <input
              v-model="form.email"
              type="email"
              id="email"
              placeholder="john@example.com"
              required
              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              Password
            </label>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                id="password"
                placeholder="••••••••"
                required
                class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700"
              >
                {{ showPassword ? 'Hide' : 'Show' }}
              </button>
            </div>
          </div>

          <div v-if="!isLogin">
            <label for="confirm-password" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
              Confirm Password
            </label>
            <input
              v-model="form.passwordConfirm"
              type="password"
              id="confirm-password"
              placeholder="••••••••"
              required
              class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-900 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            {{ error }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full h-12 px-6 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Processing...' : isLogin ? 'Login' : 'Register' }}
          </button>
        </form>

        <div class="text-center mt-8 text-gray-900 dark:text-white">
          <span v-if="isLogin">
            Don't have an account?
            <button @click="isLogin = false" class="font-bold text-blue-600 hover:underline">
              Register for free
            </button>
          </span>
          <span v-else>
            Already have an account?
            <button @click="isLogin = true" class="font-bold text-blue-600 hover:underline">
              Login
            </button>
          </span>
        </div>
      </div>
    </div>

    <div class="hidden md:block flex-1 bg-cover bg-center bg-backgroundImage"></div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/useAuthStore'

const authStore = useAuthStore()
const router = useRouter()

const isLogin = ref(true)
const showPassword = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

const form = ref({
  name: '',
  surname: '',
  email: '',
  password: '',
  passwordConfirm: '',
})

async function handleLogin() {
  loading.value = true
  error.value = null
  try {
    console.log('Login:', form.value.email)
    await router.push({ name: 'Home' })
  } catch (err: any) {
    error.value = err.message || 'Login failed'
  } finally {
    loading.value = false
  }
}

async function handleRegistration() {
  loading.value = true
  error.value = null

  if (form.value.password !== form.value.passwordConfirm) {
    error.value = 'Passwords do not match'
    loading.value = false
    return
  }

  try {
    console.log('Register:', form.value)
    await router.push({ name: 'Home' })
  } catch (err: any) {
    error.value = err.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-LogoLight,
.bg-LogoDark {
  background-size: contain;
}
</style>
