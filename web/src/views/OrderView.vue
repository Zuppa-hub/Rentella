<template>
    <body class="h-screen">
        <TopBar />
        <div class="flex h-full map-container">
            <Sidebar :apiData="OrderData" :title="title" :subtitle="subtitle" :componentType="'OrderCard'"/>
        </div>
    </body>
</template>
<script lang="ts">
import KeyCloakService from "../KeycloakService";
import TopBar from '../components/TopBar.vue';
import Sidebar from '../components/Sidebar.vue';
export default {
    name: "Order",
    components: {
        Sidebar,
        TopBar,
    },
    data() {
        return {
            OrderData: [],
            token: "",
            title: "List of orders",
            subtitle: "Number of orders: ",
        };
    },
    methods: {
        async fetchData() {
            const headers = {
                'Authorization': `Bearer ${this.token}`,
            };
            try {
                const response = await fetch('http://localhost:9000/public/api/orders?userId=1&active=true', { headers });
                this.OrderData = await response.json();
            } catch (error) {
                console.error(error);
            }
        },
    },
    created() {
        const token = KeyCloakService.GetAccesToken();
        this.token = token ? token : "";
        this.fetchData();
    },
}
</script>