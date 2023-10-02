<script setup lang="ts">
import TopBar from '@/components/TopBar.vue';

</script>

<template>
  <body class="h-screen">
      <TopBar />
    <div class="flex flex-row h-full">
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full">
        <!-- Contenuto della prima colonna -->
        
      </div>
      <div class="hidden md:flex flex-3 md:w-4/5 bg-cover bg-center "></div>
    </div>
  </body>
</template>
