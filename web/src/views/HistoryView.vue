<template>
    <body class="h-screen">
        <TopBar />
        <div class="md:flex h-full map-container">
            <div class="hidden md:block md:flex-auto md:basis-1/4 h-full">
                <leftPagePanel :title="'Orders history'" :total="OrderData.length"
                    :bottomButtonTitle="'Looking for Previous Orders?'" :bottomButtonText="'History'" />
            </div>
            <div class="md:basis-3/4 h-full">
                <Sidebar :apiData="OrderData" :title="title" :subtitle="subtitle" :componentType="'OrderCard'"
                    :roundedCornerFlag="true" :searchBarTitle="'in the history of orders'"
                    :ModalContentComponent="'OrderModalDetail'" />
            </div>
        </div>
        <NavBar />
    </body>
</template>
<script lang="ts">
import KeyCloakService from "../KeycloakService";
import TopBar from '../components/TopBar.vue';
import Sidebar from '../components/Sidebar.vue';
import { apiHelper } from '../apiService';
import NavBar from "../components/NavBar.vue";
import leftPagePanel from "../components/leftPagePanel.vue";
// The `interface UserData` is defining the structure of an object that represents user data. It
// specifies that the object should have a property called `id` of type `number`. This interface is
// used to ensure that the `UserData` object has the required properties and types when it is used in
// the component.
interface UserData {
    id: number;
}
export default {
    name: "History",
    components: {
        Sidebar,
        TopBar,
        NavBar,
        leftPagePanel,
    },
    data() {
        return {
            OrderData: [],
            UserData: {} as UserData,
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
                this.UserData = await apiHelper(apiUrl, "GET");
                this.fetchOrderData(this.UserData.id);
            } catch (error) {
                console.error(error);
            }
        },
        async fetchOrderData(userId: Number) {
            const apiUrl = `http://localhost:9000/public/api/orders?userId=${userId}`;
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