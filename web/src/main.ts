import './assets/style.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import KeyCloakService from "./KeycloakService";

const app = createApp(App)
const pinia = createPinia()

// Register Pinia for state management
app.use(pinia)

// Use the router
app.use(router)

// Funzione per il rendering dell'app
const renderApp = () => {
    app.mount('#app');
};

// Chiama il servizio KeyCloak per l'autenticazione
KeyCloakService.CallLogin(renderApp);


//app.mount('#app');
