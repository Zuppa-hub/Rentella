<template>
    <body class="h-screen ">
        <TopBar />
        <div class="flex md:flex-row flex-col map-container">
            <div class=" md:hidden flex-1 h-2/5 -ml-8 z-0">
                <Map :apiData="beachData" />
            </div>
            <Sidebar :apiData="beachData" :title="cityName" :subtitle="'Number of beaches:'" :componentType="'beachCard'"
                :searchBarTitle="'beaches'" :-modal-content-component="''" />
            <div class="hidden md:block md:h-4/5 md:w-4/5 md:bg-cover md:bg-center md:-ml-8 z-0">
                <Map :apiData="beachData" />
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
    name: "BeachSelection",
    components: {
        TopBar,
        NavBar,
        Modal,
        Map,
        Sidebar,
    },
    data() {
        return {
            beachData: [],
            beachId: this.$route.params.id,
            cityName: this.$route.query.cityName.toString() ?? "",
            distance: this.$route.query.distance,
            toggleModal: false, // Imposta questa variabile su true per aprire la modal
            beachSpecificDataForMap: [],
        };
    },
    methods: {
        manageModal() {
            this.toggleModal = !this.toggleModal; // Chiude la modal impostando la variabile su false
        },
        async fetchData() {
            const apiUrl = `http://localhost:9000/public/api/beaches?cityId=${this.beachId}`;
            try {
                this.beachData = await apiHelper(apiUrl, "GET");
            } catch (error) {
                console.error(error);
            }
        },
    },
    created() {
        this.fetchData();
    },
}

</script>