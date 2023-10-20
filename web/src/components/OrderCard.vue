<template>
    <a
        class="flex items-center justify-center p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 content-center">
        <!-- Titolo a sinistra -->
        <div class="flex-1 "> <!-- Aggiunto "items-center" per allineamento verticale -->
            <div class="flex items-baseline">
                <div class="flex-1">
                    <div class="flex-row">
                        <p>Beach zone</p>
                        <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{
                            item.name }}</p>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex-row">
                        <p>Order Id</p>
                        <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">#{{
                            item.orders.id }}</p>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex-row">
                        <p>Date</p>
                        <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{
                            item.orders.start_date }}</p>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex-none"> <!-- Aggiunto "items-center" per allineamento verticale -->
            <svg height="24" width="24" class="bg-ArrowIcon" style="background-repeat: no-repeat;"></svg>
        </div>
    </a>
</template>
<script lang="ts">
export default {
    name: "OrderCard",
    props: {
        item: {
            type: Object,
            required: true,
        },
        index: {
            type: Number,
            required: true,
        }
    },
    created() {
        console.log(this.item);
    },
}
</script>