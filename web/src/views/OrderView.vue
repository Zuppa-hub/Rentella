<template>
    <body class="h-screen">
        <TopBar />
        <div class="flex h-full map-container">
            <Sidebar :apiData="OrderData" :title="title" :subtitle="subtitle" :componentType="'OrderCard'"
                :roundedCornerFlag="true" :searchBarTitle="'orders'" :bottomButtonShow="true"
                :bottomButtonTitle="'Looking for Previous Orders?'" :bottomButtonText="'History'"
                :ModalContentComponent="'OrderModalDetail'" />
        </div>
        <NavBar></NavBar>
    </body>
</template>
<script lang="ts">
import KeyCloakService from "../KeycloakService";
import TopBar from '../components/TopBar.vue';
import Sidebar from '../components/Sidebar.vue';
import { apiHelper } from '../apiService';
import Modal from '../components/Modal.vue';
import NavBar from "../components/NavBar.vue";
export default {
    name: "Order",
    components: {
        Sidebar,
        TopBar,
        Modal,
        NavBar,
    },
    data() {
        return {
            OrderData: Array,
            UserData: Array,
            token: "",
            title: "List of orders",
            subtitle: "Number of orders: ",
        };
    },
    methods: {
        async fetchUserId() {
            const uid = KeyCloakService.GetUid();
            const apiUrl = `http://localhost:9000/public/api/users/${uid}`;
            try {
                this.UserData = await apiHelper(apiUrl, "GET");
                this.fetchOrderData(this.UserData.id);
            } catch (error) {
                console.error(error);
            }
        },
        async fetchOrderData(userId: Number) {
            const apiUrl = `http://localhost:9000/public/api/orders?userId=${userId}&active=true`;
            try {
                this.OrderData = await apiHelper(apiUrl, "GET");
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