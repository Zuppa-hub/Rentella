<script lang="ts">
import TopBar from '../components/TopBar.vue';
import NavBar from '../components/NavBar.vue';
import Map from '../components/Map.vue';
import KeyCloakService from "../KeycloakService";
import Modal from "../components/Modal.vue";
import Sidebar from '../components/Sidebar.vue';

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
      title: "Avaiable Locations",
      subtitle: "Number of location: ",
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
      const headers = {
        'Authorization': `Bearer ${this.token}`,
      };
      try {
        const response = await fetch('http://localhost:9000/public/api/locations?minLatitude=-200&maxLatitude=200&minLongitude=-200&maxLongitude=200&myLatitude=100&myLongitude=-134', { headers });
        this.LocationData = await response.json();
        console.log(this.LocationData);
      } catch (error) {
        console.error(error);
      }
    },
  },
  created() {
    const token = KeyCloakService.GetAccesToken();
    this.token = token ? token : "";
    this.fetchData();
    console.log(this.token);
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
      <Sidebar :apiData="LocationData" :title="title" :subtitle="subtitle" :componentType="'LocationCard'"/>
      <div class="hidden md:flex md:w-4/5 bg-cover bg-center -ml-8 z-0">
        <Map :apiData="LocationData" />
      </div>
    </div>
    <NavBar />
  </body>
</template>