import { createApp } from 'vue'
import App from './App.vue'

createApp(App).mount('#app')
const app = createApp(App)
import KeyCloakService from "./KeycloakService";

const renderApp = () => {
  createApp(App).mount("#app");
};

KeyCloakService.CallLogin(renderApp);