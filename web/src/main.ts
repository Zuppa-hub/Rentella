import { createApp } from 'vue'
import App from './App.vue'
import { initKeycloak } from './keycloak'
import { i18n } from './i18n'
import 'leaflet/dist/leaflet.css'

initKeycloak()
  .catch((error) => {
    console.error('Keycloak init failed', error)
  })
  .finally(() => {
    return createApp(App).use(i18n).mount('#app')
  })
