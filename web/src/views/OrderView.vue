<template>
    <body class="h-screen">
        <TopBar />
        <div class="md:flex h-full map-container ">
            <div class="hidden md:block md:flex-auto md:basis-1/4 h-full">
                <leftPagePanel :title="'Orders'" :total="OrderData.length"
                    :bottomButtonTitle="'Looking for Previous Orders?'" :bottomButtonText="'History'"
                    :favouriteBeach="favourite.name" :favouriteLocation="FavouriteCity" />
            </div>
            <div class="md:basis-3/4 h-full">
                <Sidebar :apiData="OrderData" :title="title" :subtitle="subtitle" :componentType="'OrderCard'"
                    :roundedCornerFlag="true" :searchBarTitle="'orders'" :ModalContentComponent="'OrderModalDetail'" />
            </div>
        </div>
        <NavBar />
    </body>
</template>
<script lang="ts">
import KeyCloakService from "../KeycloakService";
import TopBar from '../components/TopBar.vue';
import Sidebar from '../components/Sidebar.vue';
import { apiHelper, findMostFrequentElement } from '../apiService';
import Modal from '../components/Modal.vue';
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
    name: "Order",
    components: {
        Sidebar,
        TopBar,
        Modal,
        NavBar,
        leftPagePanel,
    },
    data() {
        return {
            OrderData: [],
            // The line `UserData: {} as UserData` is initializing the `UserData` property as an empty object and
            // asserting its type to be `UserData`. This is done to ensure that the `UserData` object has the
            // required properties and types specified in the `UserData` interface.
            UserData: {} as UserData,
            token: "",
            title: "List of orders",
            subtitle: "Number of orders: ",
            favourite: [],
            FavouriteCity: "",
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
                this.favourite = findMostFrequentElement(this.OrderData);
                this.FavouriteCity = this.favourite.orders.umbrella.beachzone.beach.location.city_name;
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