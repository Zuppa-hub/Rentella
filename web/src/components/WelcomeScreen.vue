<template>
  <div class="welcome-screen">
    <div class="welcome-content">
      <div class="welcome-header">
        <img :src="logoDark" :alt="t('desktop.brand.alt')" class="welcome-logo" />
        <h1>{{ t('welcome.title') || 'Rentella' }}</h1>
        <p class="welcome-subtitle">{{ t('welcome.subtitle') || 'Discover the best beaches near you' }}</p>
      </div>

      <div class="welcome-features">
        <div class="feature">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" fill="currentColor" />
            <path d="M12 6c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z" fill="currentColor" />
          </svg>
          <p>{{ t('welcome.feature1') || 'Find nearby beaches' }}</p>
        </div>
        <div class="feature">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor" />
          </svg>
          <p>{{ t('welcome.feature2') || 'Check real-time prices' }}</p>
        </div>
        <div class="feature">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.3-1.54-4.6 5.86h17.6L13.96 12.29z" fill="currentColor" />
          </svg>
          <p>{{ t('welcome.feature3') || 'See beach photos' }}</p>
        </div>
      </div>

      <button class="login-button" @click="$emit('login')">
        {{ t('welcome.login') || 'Login with Keycloak' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import logoDark from '../assets/LogoDark.svg'

defineEmits<{
  login: []
}>()

const { t } = useI18n()
</script>

<style scoped>
.welcome-screen {
  width: 100%;
  height: 100vh;
  background: linear-gradient(135deg, #005f6f 0%, #0b5f6f 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.welcome-content {
  max-width: 500px;
  width: 100%;
  text-align: center;
  color: white;
}

.welcome-header {
  margin-bottom: 48px;
}

.welcome-logo {
  height: 60px;
  width: auto;
  margin-bottom: 24px;
}

.welcome-header h1 {
  font-size: 32px;
  font-weight: 700;
  margin: 0 0 8px 0;
  font-family: 'Space Grotesk', sans-serif;
}

.welcome-subtitle {
  font-size: 16px;
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
  font-family: 'Inter', sans-serif;
}

.welcome-features {
  display: grid;
  grid-template-columns: 1fr;
  gap: 24px;
  margin-bottom: 48px;
}

.feature {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
}

.feature svg {
  color: #d2eef1;
}

.feature p {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.9);
  margin: 0;
  font-family: 'Inter', sans-serif;
}

.login-button {
  width: 100%;
  padding: 14px 28px;
  background: white;
  color: #005f6f;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  font-family: 'Inter', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
}

.login-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.login-button:active {
  transform: translateY(0);
}

@media (max-width: 1024px) {
  .welcome-header h1 {
    font-size: 28px;
  }

  .welcome-subtitle {
    font-size: 14px;
  }

  .welcome-features {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}
</style>
