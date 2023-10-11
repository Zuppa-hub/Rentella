import './assets/style.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import KeyCloakService from "./KeycloakService";
import axios from "axios";

const app = createApp(App)

// Usa il router
app.use(router)

// Funzione per il rendering dell'app
const renderApp = () => {
    app.mount('#app');
};

// Chiama il servizio KeyCloak per l'autenticazione
KeyCloakService.CallLogin(renderApp);

//axios bearer token
axios.interceptors.request.use(config => {
    const token = localStorage.getItem("jwt");
    config.headers["Authorization"] = `Bearer ${KeyCloakService.GetAccesToken()}`;
    return config;
});