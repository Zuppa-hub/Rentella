<template>
    <body class="h-screen">
        <TopBar />
        <div class="flex h-full map-container">
            <Sidebar :apiData="OrderData" :title="title" :subtitle="subtitle" :componentType="'OrderCard'"
                :roundedCornerFlag="true" :searchBarTitle="'in the history of orders'" :bottomButtonShow="true"
                :bottomButtonTitle="'Looking for Active Orders?'" :bottomButtonText="'Orders'"
                :ModalContentComponent="'OrderModalDetail'" />
        </div>
        <NavBar></NavBar>
    </body>
</template>
<script lang="ts">
import KeyCloakService from "../KeycloakService";
import TopBar from '../components/TopBar.vue';
import Sidebar from '../components/Sidebar.vue';
import { fetchData } from '../apiService';
import NavBar from "../components/NavBar.vue";
export default {
    name: "History",
    components: {
        Sidebar,
        TopBar,
        NavBar,
    },
    data() {
        return {
            OrderData: {
                type: Object,
                default: [],
            },
            UserData: {
                type: Object,
                default: [],
            },
            token: "",
            title: "History of orders",
            subtitle: "Number of orders: ",
        };
    },
    methods: {
        async fetchUserId() {
            const uid = KeyCloakService.GetUid();
            const apiUrl = `http://localhost:9000/public/api/users/${uid}`;
            try {
                this.UserData = await fetchData(apiUrl);
                this.fetchOrderData(this.UserData.id);
            } catch (error) {
                console.error(error);
            }
        },
        async fetchOrderData(userId: Number) {
            const apiUrl = `http://localhost:9000/public/api/orders?userId=${userId}`;
            try {
                this.OrderData = await fetchData(apiUrl);
            } catch (error) {
                console.error(error);
            }
        },

    },
    created() {
        this.fetchUserId();
    },
}
</script>