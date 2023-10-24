<template>
    <a
        class="flex p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-black dark:border-black dark:hover:bg-black content-center">
        <!-- Titolo a sinistra -->
        <div class="flex-1">
            <div class="flex items-baseline">
                <p class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ item.name }}
                </p>
            </div>

            <div class="flex">
                <div class="flex-none">
                    <div class="flex justify-center items-center">
                        <svg height="16" width="24" class="bg-UmbrellaIcon stroke-black"
                            style="background-repeat: no-repeat;"></svg>
                        <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.umbrellas_count }}</p>
                    </div>
                </div>

                <div class="flex-1">
                    <div class="flex justify-center items-center">
                        <svg height="16" width="24" class="bg-MoneyIcon stroke-black"
                            style="background-repeat: no-repeat;"></svg>
                        <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.prices.price }}</p>
                    </div>
                </div>
            </div>
            <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.description }}</p>
        </div>
        <div class="cursor-pointer">
            <svg height="24" width="24" class="fill-black bg-ArrowIcon dark:bg-ArrowIconDark "
                style="background-repeat: no-repeat;"></svg>
        </div>
    </a>
</template>
  
  
<script lang="ts">
export default {
    name: "ZoneCard",
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
};
</script>
  