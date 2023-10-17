<template>
    <a
        class="flex items-center justify-center p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 content-center">
        <!-- Titolo a sinistra -->
        <div class="flex-1 "> <!-- Aggiunto "items-center" per allineamento verticale -->
            <div class="flex items-baseline">
                <div class="w-10 h-10 mr-2 bg-white rounded-full flex items-center justify-center">
                    <span class="text-black text-xl font-semibold">{{ index }}</span>
                </div>
                <p class="mb-2 text-l font-bold tracking-tight text-gray-900 dark:text-white">{{
                    item.name }}</p>
            </div>
        </div>
        <div class="flex-1 "> <!-- Aggiunto "items-center" per allineamento verticale -->
            <div class="flex justify-center items-center">
                <svg height="16" width="24" class="bg-MoneyIcon" style="background-repeat: no-repeat;"></svg>
                <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.min_price }} - {{
                    item.max_price }}</p>
            </div>
        </div>
        <!-- Km a destra -->
        <div class="flex-1"> <!-- Aggiunto "items-center" per allineamento verticale -->
            <div class="flex justify-center items-center">
                <svg height="16" width="24" class="bg-DistanceIcon" style="background-repeat: no-repeat;"></svg>
                <p class="text-right font-normal text-gray-700 dark:text-gray-400">{{ item.distance }}.</p>
            </div>
        </div>
        <div class="flex-none"> <!-- Aggiunto "items-center" per allineamento verticale -->
            <svg height="24" width="24" class="bg-ArrowIcon" style="background-repeat: no-repeat;"></svg>
        </div>
    </a>
</template>
<script lang="ts">
export default {
    name: "LocationCard",
    props: {
        item: {
            type: Array,
            required: true,
        },
        index: {
            type: Number,
            required: true,
        }
    }
}

</script>