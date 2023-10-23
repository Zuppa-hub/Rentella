<template>
  <body class="h-screen ">
    <TopBar />
    <div class="flex md:flex-row flex-col map-container">
      <div class=" md:hidden flex-1 h-2/5 -ml-8 z-0">
        <Map :apiData="LocationData" />
      </div>
      <Sidebar :apiData="LocationData" :title="title" :subtitle="subtitle" :componentType="'LocationCard'"
        :searchBarTitle="'cities'" :-modal-content-component="'LocationModalDetail'" />
      <div class="hidden md:block md:h-4/5 md:w-4/5 md:bg-cover md:bg-center md:-ml-8 z-0">
        <Map :apiData="LocationData" />
      </div>
    </div>
    <NavBar />
  </body>
</template>

<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import Map from '../components/Map.vue';
import Sidebar from '../components/Sidebar.vue';
import { apiHelper } from '../apiService';
import Modal from '../components/Modal.vue';

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
      maxLatitude: 200,
      minLatitude: -200,
      maxLongitude: 200,
      minLongitude: -200,
      toggleModal: false, // Imposta questa variabile su true per aprire la modal
      modalTitle: "Titolo della Modal",
    };
  },
  methods: {
    manageModal() {
      this.toggleModal = !this.toggleModal; // Chiude la modal impostando la variabile su false
    },
    async fetchData() {
      const apiUrl = `http://localhost:9000/public/api/locations?minLatitude=${this.minLatitude}&maxLatitude=${this.maxLatitude}&minLongitude=${this.minLongitude}&maxLongitude=${this.maxLongitude}&myLatitude=100&myLongitude=-134`;
      try {
        this.LocationData = await apiHelper(apiUrl, "GET");
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


