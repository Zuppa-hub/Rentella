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
      searchTerm: "",
      toggleModal: true
    };
  },
  methods: {
    async fetchData() {
      const headers = {
        'Authorization': `Bearer ${this.token}`,
      };
      try {
        const response = await fetch('http://localhost:9000/public/api/locations ', { headers });
        this.apiData = await response.json();
        console.log(this.apiData);
      } catch (error) {
        console.error(error);
      }
    },
    filterItems() {
      if (!this.apiData || !this.searchTerm) {
        return this.apiData;
      }

      const searchTermLower = this.searchTerm.toLowerCase();

      return this.apiData.filter(item => {
        return item.city_name.toLowerCase().includes(searchTermLower);
      });
    },

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
    <div v-if="toggleModal" class="absolute inset-0 z-40 opacity-25 bg-black">
    </div>
    <div v-if="toggleModal" class="fixed  "></div>
    <TopBar />
    <div class="flex flex-row h-full map-container md:calc(100vh - 72px)">
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full rounded-corner">
        <div class="mx-4 flex-row items-start justify-start  dark:text-white">
          <form class="py-3 sm:py-4 ">
            <div class="relative ">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
              </div>
              <input type="search" id="default-search"
                class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                v-model="searchTerm" placeholder="Search cities" required>
            </div>
          </form>
          <div class="flex">
            <h2 class="text-xl font-bold flex-1">Available Locations</h2>
            <p class="flex-1 text-sm text-gray-500 dark:text-gray-400">Showing {{ apiData?.length }} locations
            </p>
          </div>

          <ul class="divide-gray-500 dark:divide-gray-700">
            <li class="pb-3 sm:pb-4 " v-for="(item, index) in filterItems()" :key="index">
              <a href="#"
                class="flex p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

                <!-- Titolo a sinistra -->
                <div class="flex-1">
                  <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{ item.city_name }}
                  </p>
                </div>

                <div class="flex-1">
                  <p class="flex-1 text-center font-normal text-gray-700 dark:text-gray-400">100-200</p>
                </div>
                <!-- Numeri al centro -->

                <!-- Km a destra -->
                <div class="flex-1">
                  <p class="text-right font-normal text-gray-700 dark:text-gray-400">23km.</p>
                </div>
              </a>
            </li>
          </ul>



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
