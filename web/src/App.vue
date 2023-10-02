<script lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import Login from './views/Login.vue';
import TopBar from './components/TopBar.vue';
export default {
  name: 'App',
  components: {
    TopBar,
  }
}
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <RouterView />
  </div>
</template>

<style scoped></style>
