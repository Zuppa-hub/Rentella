<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import Map from '../components/Map.vue';
import KeyCloakService from "../KeycloakService";
import Modal from "../components/Modal.vue";

export default {
  name: "Home",
  components: {
    TopBar,
    NavBar,
    Modal,
    Map,
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
    <div class="flex h-full map-container">
      <div class=" dark:bg-gray-950 bg-white overflow-y-auto rounded-corner z-40 w-full">
        <div class="mx-4 items-start justify-start  dark:text-white">
          <form class="py-3 sm:py-4 sticky top-0 dark:bg-gray-950 bg-white">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
              </div>
              <input type="search" id="default-search"
                class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 "
                v-model="searchTerm" placeholder="Search cities" required>
            </div>
          </form>
          <div class="flex items-baseline my-3">
            <div class="flex-1">
              <p class="text-xl font-bold">Available Locations</p>
            </div>
            <div class="flex-1">
              <p class="text-sm text-gray-500 dark:text-gray-400 text-right">Showing {{ apiData?.length }}
                locations
              </p>
            </div>
          </div>
          <ul class="divide-gray-500 dark:divide-gray-700 flex flex-col">
            <li class="pb-4 mb-4" v-for="(item, index) in filterItems()" :key="index">
              <a
                class="flex items-center justify-center p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 content-center">
                <!-- Titolo a sinistra -->
                <div class="flex-1 "> <!-- Aggiunto "items-center" per allineamento verticale -->
                  <div class="flex items-baseline">
                    <div class="w-10 h-10 mr-2 bg-white rounded-full flex items-center justify-center">
                      <span class="text-black text-xl font-semibold">{{ index }}</span>
                    </div>
                    <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{ item.city_name }}</p>
                  </div>
                </div>
                <div class="flex-1 "> <!-- Aggiunto "items-center" per allineamento verticale -->
                  <div class="flex justify-center items-center">
                    <svg height="16" width="24" class="bg-MoneyIcon" style="background-repeat: no-repeat;"></svg>
                    <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.min_price }} - {{
                      item.max_price }}</p>
                  </div>
                </div>
                <!-- Km a destra -->
                <div class="flex-1"> <!-- Aggiunto "items-center" per allineamento verticale -->
                  <div class="flex justify-center items-center">
                    <svg height="16" width="24" class="bg-DistanceIcon" style="background-repeat: no-repeat;"></svg>
                    <p class="text-right font-normal text-gray-700 dark:text-gray-400">{{ item.distance }}.</p>
                  </div>
                </div>
                <div class="flex-none"> <!-- Aggiunto "items-center" per allineamento verticale -->
                  <svg height="24" width="24" class="bg-ArrowIcon" style="background-repeat: no-repeat;"></svg>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="hidden md:flex md:w-4/5 bg-cover bg-center -ml-8 z-0">
        <Map :apiData="apiData" />
      </div>
    </div>
    <NavBar />
  </body>
</template>