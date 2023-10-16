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
      <div
    class="relative bg-gray-50 dark:bg-slate-900 w-screen h-screen pattern"
>
    <nav
    class="z-20 flex shrink-0 grow-0 justify-around gap-4 border-t border-gray-200 bg-white/50 p-2.5 shadow-lg backdrop-blur-lg dark:border-slate-600/60 dark:bg-slate-800/50 fixed top-2/4 -translate-y-2/4 left-6 min-h-[auto] min-w-[64px] flex-col rounded-lg border"
    >
    <a
        href="#profile"
        class="flex aspect-square min-h-[32px] w-16 flex-col items-center justify-center gap-1 rounded-md p-1.5 bg-indigo-50 text-indigo-600 dark:bg-sky-900 dark:text-sky-50"
    >
        <!-- HeroIcon - User -->
        <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6 shrink-0"
        >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"
        />
        </svg>

        <small class="text-center text-xs font-medium"> Profile </small>
    </a>

    <a
        href="#analytics"
        class="flex aspect-square min-h-[32px] w-16 flex-col items-center justify-center gap-1 rounded-md p-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800"
    >
        <!-- HeroIcon - Chart Bar -->
        <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6 shrink-0"
        >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"
        />
        </svg>

        <small class="text-center text-xs font-medium"> Analytics </small>
    </a>

    <a
        href="#settings"
        class="flex aspect-square min-h-[32px] w-16 flex-col items-center justify-center gap-1 rounded-md p-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-slate-800"
    >
    <!-- HeroIcon - Cog-6-tooth -->
        <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6 shrink-0"
        >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"
        />
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
        />
        </svg>

        <small class="text-center text-xs font-medium"> Settings </small>
    </a>

    <hr class="dark:border-gray-700/60" />

    <a
        href="/"
        class="flex h-16 w-16 flex-col items-center justify-center gap-1 text-fuchsia-900 dark:text-gray-400"
    >
    <!-- HeroIcon - Home Modern -->
        <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="w-6 h-6"
        >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819"
        />
        </svg>

        <small className="text-xs font-medium">Home</small>
    </a>
    </nav>
</div>
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
