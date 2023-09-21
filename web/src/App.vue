<template>
  <div id="app">
    <router-view></router-view>
    <Login v-if="showLoginComponent" />
  </div>
</template>


<script lang="ts">
import Login from './components/Login.vue'
export default
  {
    data() {
      return {
        showLoginComponent: true, // Cambia questa variabile in base alla tua logica
      };
    },
    components: {
      Login, // Registra il componente Login
    },
  };
</script>