<template >
    <div class="scrollable-content">
        <modalInfoTile :title="'Order Id'" :detail="'# ' + item.orders.id"></modalInfoTile>
        <modalInfoTile :title="'Location'" :detail="item.orders.umbrella.beachzone.beach.location.city_name">
        </modalInfoTile>
        <modalInfoTile :title="'Section'" :detail="item.orders.umbrella.beachzone.name"></modalInfoTile>
        <modalInfoTile :title="'Umbrella'" :detail="item.orders.umbrella.number.toString()"></modalInfoTile>
        <modalInfoTile :title="'Price'" :detail="item.orders.price.price + '€'"></modalInfoTile>
        <modalInfoTile :title="'Duration'"
            :detail="formatDate(item.orders.start_date) + ' - ' + formatDate(item.orders.end_date)"></modalInfoTile>
        <hr class="border-b border-gray-200 dark:border-gray-800 mt-4" />
        <div class="flex dark:text-white text-xl">
            <div class="flex-1">
                <p> <b>Total: </b></p>
            </div>
            <div class="flex-none">
                <p><b> {{ item.orders.price.price }}</b></p>
            </div>
        </div>

    </div>
    <div class="flex dark:text-white mt-4">
        <div class="flex-1">
            <button @click="closeModal" class="w-full dark:text-white font-medium mt-2">Back</button>
        </div>
        <div class="flex-1 " v-if="isStartDatePassed()">
            <button @click="cancelReservation(item.orders.id)"
                class="w-full text-white bg-primary font-medium rounded-lg text-sm px-6 py-2.5">Cancel
                reservation</button>
        </div>
    </div>
</template>
<script lang="ts">
import modalInfoTile from './modalInfoTile.vue';
import { apiHelper } from '../apiService';
export default {
    name: "OrderModalDetail",
    components: {
        modalInfoTile
    },
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
    emits: ['close-modal',],
    methods: {
        closeModal() {
            this.$emit('close-modal');
        },
        async cancelReservation(id: Number) {
            const apiUrl = `http://localhost:9000/public/api/orders/${id}`;
            try {
                await apiHelper(apiUrl, "DELETE");
                //this.closeModal();
                this.$router.go(0)
            } catch (error) {
                console.error(error);
            }
        },
        formatDate(dateString: string): string {
            const options: Intl.DateTimeFormatOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            };
            const date = new Date(dateString);
            return date.toLocaleDateString(undefined, options);
        },
        isStartDatePassed() {
            const startDate = new Date(this.item.orders.start_date);
            const currentDate = new Date();
            return startDate > currentDate;
        },
    },

}
</script>
