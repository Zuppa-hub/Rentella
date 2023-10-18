<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import Map from '../components/Map.vue';
import Modal from "../components/Modal.vue";
import Sidebar from '../components/Sidebar.vue';
import { fetchData } from '../apiService';

export default {
  name: "Home",
  components: {
    TopBar,
    NavBar,
    Modal,
    Map,
    Sidebar,
  },
  data() {
    return {
      zoom: 10,
      title: "Available Locations",
      subtitle: "Number of locations: ",
      LocationData: [],
      token: "",
      toggleModal: false,
      maxLatitude: 200,
      minLatitude: -200,
      maxLongitude: 200,
      minLongitude: -200,
    };
  },
  methods: {
    async fetchData() {
      const apiUrl = `http://localhost:9000/public/api/locations?minLatitude=${this.minLatitude}&maxLatitude=${this.maxLatitude}&minLongitude=${this.minLongitude}&maxLongitude=${this.maxLongitude}&myLatitude=100&myLongitude=-134`;
      try {
        this.LocationData = await fetchData(apiUrl);
        console.log(this.LocationData);
      } catch (error) {
        console.error(error);
      }
    },
  },
  created() {
    this.fetchData();
  }
}
</script>


<template>
  <body class="h-screen">
    <div v-if="toggleModal" class="absolute inset-0 z-40 opacity-25 bg-black">
    </div>
    <div v-if="toggleModal" class="fixed  "></div>
    <TopBar />
    <div class="flex h-full map-container">
      <Sidebar :apiData="LocationData" :title="title" :subtitle="subtitle" :componentType="'LocationCard'" />
      <div class="hidden md:flex md:w-4/5 bg-cover bg-center -ml-8 z-0">
        <Map :apiData="LocationData" />
      </div>
    </div>
    <NavBar />
  </body>
</template>