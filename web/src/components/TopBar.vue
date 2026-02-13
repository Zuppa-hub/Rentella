<template>
  <header class="topbar">
    <img :src="logo" alt="Rentella" class="logo" />
    <button class="profile" @click="handleAuth">
      <span v-if="authenticated">{{ initials }}</span>
      <span v-else>Login</span>
    </button>
  </header>
</template>

<script setup lang="ts">
import logo from '../assets/LogoLight.svg'

const props = defineProps<{ authenticated: boolean; initials: string }>()
const emit = defineEmits<{ (event: 'login'): void; (event: 'logout'): void }>()

const handleAuth = () => {
  if (props.authenticated) {
    emit('logout')
    return
  }
  emit('login')
}
</script>

<style scoped>
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: calc(12px + env(safe-area-inset-top)) clamp(12px, 4vw, 32px) 12px;
  min-height: 64px;
  width: 100%;
  box-sizing: border-box;
}

.logo {
  height: 24px;
  filter: brightness(0) invert(1);
}

.profile {
  background: #ffffff;
  border: none;
  border-radius: 999px;
  width: 40px;
  height: 40px;
  padding: 0;
  font-weight: 700;
  color: #0b5f6f;
  min-width: 40px;
  cursor: pointer;
  display: grid;
  place-items: center;
}
</style>
