import './assets/style.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons';


const app = createApp(App)
import KeyCloakService from "./KeycloakService";
library.add(faEye, faEyeSlash);
app.component('font-awesome-icon', FontAwesomeIcon);

app.use(router)
const renderApp = () => {
    createApp(App).mount("#app");
};
app.mount('#app')

KeyCloakService.CallLogin(renderApp);