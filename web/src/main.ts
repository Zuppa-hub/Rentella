import './assets/style.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import KeyCloakService from "./KeycloakService";

const app = createApp(App)

// Usa il router
app.use(router)

// Funzione per il rendering dell'app
const renderApp = () => {
    app.mount('#app');
};

// Chiama il servizio KeyCloak per l'autenticazione
KeyCloakService.CallLogin(renderApp);


//app.mount('#app');
