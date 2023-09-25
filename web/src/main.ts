import { createApp } from 'vue'
import App from './App.vue'
import "./assets/style.css"

createApp(App).mount('#app')
const app = createApp(App)
import KeyCloakService from "./KeycloakService";

const renderApp = () => {
  createApp(App).mount("#app");
};

KeyCloakService.CallLogin(renderApp);