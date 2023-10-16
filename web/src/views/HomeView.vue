<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import KeyCloakService from "../KeycloakService";
import Modal from "../components/Modal.vue";
import { ref } from 'vue';
import "leaflet/dist/leaflet.css";
import type L from "leaflet";
import { LMap, LTileLayer, LMarker, LPopup } from "@vue-leaflet/vue-leaflet";

export default {
  name: "Home",
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup,
    TopBar,
    NavBar,
    Modal
  },
  data() {
    return {
      zoom: 10,
      apiData: [],
      token: "",
      searchTerm: "",
      toggleModal: false,
      maxLatitude: 200,
      minLatitude: -200,
      maxLongitude: 200,
      minLongitude: -200,
      myLatitude: ref(0),
      myLongitude: ref(0),
    };
  },
  methods: {
    async fetchData() {
      const headers = {
        'Authorization': `Bearer ${this.token}`,
      };
      try {
        const response = await fetch('http://localhost:9000/public/api/locations?minLatitude=-200&maxLatitude=200&minLongitude=-200&maxLongitude=200&myLatitude=100&myLongitude=-134', { headers });
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
    // The `Geolocate()` method is used to get the current latitude and longitude coordinates of the
    // user's device using the Geolocation API.
    Geolocate() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          this.myLatitude = position.coords.latitude;
          this.myLongitude = position.coords.longitude;
          console.log(this.myLatitude, this.myLongitude);
        });
      }
    }
  },
  created() {
    const token = KeyCloakService.GetAccesToken();
    this.token = token ? token : "";
    this.fetchData();
    this.Geolocate();
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
      
      <div class="md:w-2/5 dark:bg-gray-950 overflow-y-auto w-full rounded-corner z-40">
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
            <div class="flex-1">
              <h2 class="text-xl font-bold flex-1 ">Available Locations</h2>
            </div>
            <div class="flex-1">
              <p class="flex-1 text-sm text-gray-500 dark:text-gray-400 text-right">Showing {{ apiData?.length }}
                locations
              </p>
            </div>
          </div>


          <ul class="divide-gray-500 dark:divide-gray-700">
            <li class="pb-3 sm:pb-4 " v-for="(item, index) in filterItems()" :key="index">
              <a href="#"
                class="flex p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">

                <!-- Titolo a sinistra -->
                <div class="flex">
                  <svg height="24" width="24" class="bg-HomeIcon mx-2" style="background-repeat: no-repeat;">
                  </svg>
                  <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{ item.city_name }}
                  </p>
                </div>

                <div class="flex">
                  <svg height="24" width="24" class="bg-MoneyIcon " style="background-repeat: no-repeat;">
                  </svg>
                  <p class="flex-1 text-center font-normal text-gray-700 dark:text-gray-400">{{ item.min_price }} - {{
                    item.max_price }}</p>
                </div>
                <!-- Numeri al centro -->

                <!-- Km a destra -->
                <div class="flex">
                  <svg height="24" width="24" class="bg-DistanceIcon " style="background-repeat: no-repeat;">
                  </svg>
                  <p class="text-right font-normal text-gray-700 dark:text-gray-400">{{ item.distance }}.</p>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="hidden md:flex md:w-4/5 bg-cover bg-center -ml-8 z-0">
        <div class="map-container">
          <l-map ref="map" v-model:zoom="zoom" :center="[myLatitude, myLongitude]" :options="{ zoomControl: true, }">
            <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" layer-type="base"
              name="OpenStreetMap"></l-tile-layer>
            <l-control-zoom position="bottomright"></l-control-zoom>
            <!-- Utilizza v-for per creare marker e popup per ogni elemento in apiData -->
            <l-marker v-for="(item, index) in apiData" :key="index" :lat-lng="[item.latitude, item.longitude]">
              <l-popup>{{ item.city_name }}</l-popup>
            </l-marker>
          </l-map>

        </div>
      </div>
    </div>
    <NavBar />
  </body>
</template>
<style scoped>
.pattern {
  /* From - https://bgjar.com/meteor */
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' version='1.1' xmlns:xlink='http://www.w3.org/1999/xlink' xmlns:svgjs='http://svgjs.com/svgjs' width='1440' height='560' opacity='0.05' preserveAspectRatio='none' viewBox='0 0 1440 560'%3e%3cg mask='url(%26quot%3b%23SvgjsMask1021%26quot%3b)' fill='none'%3e%3cpath d='M905 408L904 38' stroke-width='6' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M607 269L606 125' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M68 213L67 365' stroke-width='8' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M1397 326L1396 563' stroke-width='8' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M365 418L364 761' stroke-width='6' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M293 317L292 38' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1247 130L1246 508' stroke-width='6' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1339 5L1338 -305' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M831 7L830 -367' stroke-width='6' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M113 250L112 605' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M967 177L966 -236' stroke-width='6' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M922 538L921 160' stroke-width='6' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M1109 316L1108 32' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1281 270L1280 50' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1280 403L1279 17' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M308 106L307 490' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M517 147L516 374' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M690 30L689 341' stroke-width='10' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M614 367L613 154' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M770 486L769 188' stroke-width='6' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1336 235L1335 573' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M303 441L302 96' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M970 211L969 384' stroke-width='10' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M151 243L150 26' stroke-width='6' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M184 391L183 201' stroke-width='10' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M1299 123L1298 -228' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1024 466L1023 254' stroke-width='6' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M1385 330L1384 546' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M991 172L990 552' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M600 461L599 254' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M146 544L145 145' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M539 421L538 723' stroke-width='10' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1408 130L1407 316' stroke-width='10' stroke='url(%23SvgjsLinearGradient1022)' stroke-linecap='round' class='Up'%3e%3c/path%3e%3cpath d='M1134 160L1133 572' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M810 190L809 39' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3cpath d='M1069 388L1068 46' stroke-width='8' stroke='url(%23SvgjsLinearGradient1023)' stroke-linecap='round' class='Down'%3e%3c/path%3e%3c/g%3e%3cdefs%3e%3cmask id='SvgjsMask1021'%3e%3crect width='1440' height='560' fill='white'%3e%3c/rect%3e%3c/mask%3e%3clinearGradient x1='0%25' y1='100%25' x2='0%25' y2='0%25' id='SvgjsLinearGradient1022'%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 0)' offset='0'%3e%3c/stop%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 1)' offset='1'%3e%3c/stop%3e%3c/linearGradient%3e%3clinearGradient x1='0%25' y1='0%25' x2='0%25' y2='100%25' id='SvgjsLinearGradient1023'%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 0)' offset='0'%3e%3c/stop%3e%3cstop stop-color='rgba(255%2c 255%2c 255%2c 1)' offset='1'%3e%3c/stop%3e%3c/linearGradient%3e%3c/defs%3e%3c/svg%3e");
}
</style>
