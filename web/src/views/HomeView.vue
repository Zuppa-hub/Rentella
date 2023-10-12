<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer } from "@vue-leaflet/vue-leaflet";
import KeyCloakService from "../KeycloakService";
import Modal from "../components/Modal.vue";

export default {
  name: "Home",
  components: {
    LMap,
    LTileLayer,
    TopBar,
    NavBar,
    Modal
  },
  data() {
    return {
      zoom: 10,
      apiData: null,
      token: "",
    };
  },
  methods: {
    async fetchData() {
      const headers = {
        'Authorization': `Bearer ${this.token}`,
      };
      try {
        const response = await fetch('http://localhost:9000/public/api/beaches', { headers });
        this.apiData = await response.json();
        console.log(this.apiData);
      } catch (error) {
        console.error(error);
      }
    }

  },
  created() {
    const token = KeyCloakService.GetAccesToken();
    this.token = token ? token : "";
    this.fetchData();
  },
};
</script>

<template>
  <body class="h-screen">
    <TopBar />
    <div class="flex flex-row h-full map-container md:calc(100vh - 72px)">
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full rounded-corner">
        <div class="flex items-center justify-center  dark:text-white">
        </div>
      </div>
      <div class="hidden md:flex md:w-4/5 bg-cover bg-center">
        <l-map ref="map" v-model:zoom="zoom" :center="[47.41322, -1.219482]">
          <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
            name="OpenStreetMap"></l-tile-layer>
        </l-map>

      </div>
    </div>
    <NavBar />
  </body>
</template>
