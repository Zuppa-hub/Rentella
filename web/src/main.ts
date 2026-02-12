import { createApp } from 'vue'
import App from './App.vue'
import { initKeycloak } from './keycloak'
import 'leaflet/dist/leaflet.css'

initKeycloak()
  .catch((error) => {
    console.error('Keycloak init failed', error)
  })
  .finally(() => {
    return createApp(App).mount('#app')
  })
