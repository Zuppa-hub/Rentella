<script lang="ts">
import TopBar from '@/components/TopBar.vue';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer } from "@vue-leaflet/vue-leaflet";
export default {
  name: "Home",
  components: {
    LMap,
    LTileLayer,
    TopBar
  },
  data() {
    return {
      zoom: 2,
    };
  },
};
</script>

<template>
  <body class="h-screen">
    <TopBar />
    <div class="flex flex-row h-full">
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full">
        <!-- Contenuto della prima colonna -->

      </div>
      <div class="hidden md:flex flex-3 md:w-4/5 bg-cover bg-center ">
        <div style="height:600px; width:800px">
          <l-map ref="map" v-model:zoom="zoom" :center="[47.41322, -1.219482]">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
              name="OpenStreetMap"></l-tile-layer>
          </l-map>
        </div>

      </div>
    </div>
  </body>
</template>
