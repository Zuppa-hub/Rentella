<template>
    <a
        class="flex p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 content-center">
        <!-- Titolo a sinistra -->
        <div class="flex-none w-14 h-14">
            <div class="w-10 h-10 mr-2 bg-white rounded-full flex items-center justify-center">
                <span class="text-black text-xl font-semibold">{{ index }}</span>
            </div>
        </div>

        <div class="flex-1">
            <div class="flex items-baseline">
                <p class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ index + '. ' + item.beach.name }}
                </p>
            </div>

            <div class="flex">
                <div class="flex-1">
                    <div class="flex justify-center items-center">
                        <svg height="16" width="24" class="bg-BeachTypeIcon stroke-black"
                            style="background-repeat: no-repeat;"></svg>
                        <div v-if="item.beach.type_id == 1">
                            <p class="flex font-normal text-gray-700 dark:text-gray-400">Sand</p>
                        </div>
                        <div v-else>
                            <div v-if="item.beach.type_id == 2">
                                <p class="flex font-normal text-gray-700 dark:text-gray-400">Stone</p>
                            </div>
                            <div v-else>
                                <p class="flex font-normal text-gray-700 dark:text-gray-400">Mix</p>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex justify-center items-center">
                        <svg height="16" width="24" class="bg-AllowedAnimalsIcon stroke-black"
                            style="background-repeat: no-repeat;"></svg>
                        <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.beach.allowed_animals }}
                        </p>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex justify-center items-center">
                        <svg height="16" width="24" class="bg-MoneyIcon stroke-black"
                            style="background-repeat: no-repeat;"></svg>
                        <p class="flex font-normal text-gray-700 dark:text-gray-400">{{ item.beach_min_price + ' - ' +
                            item.beach_max_price }}</p>
                    </div>
                </div>
            </div>
            <div v-if="expanded" class="mt-3">
                <div v-for="(zone, index) in item.beach.zones" :key="index">
                    <ZoneCard :item="zone"></ZoneCard>
                </div>
                <!-- Contenuto espanso -->
            </div>
        </div>
        <div class="cursor-pointer" @click="toggleExpansion" v-if="expanded">
            <svg height="24" width="24" class="fill-black bg-ArrowIcon dark:bg-ArrowUpListIcon "
                style="background-repeat: no-repeat;"></svg>
        </div>
        <div v-else @click="toggleExpansion">
            <svg height="24" width="24" class="fill-black bg-ArrowIcon dark:bg-ArrowDownListIcon "
                style="background-repeat: no-repeat;"></svg>
        </div>
    </a>
</template>
  
  
<script lang="ts">
import ZoneCard from "./selectZoneCard.vue"
export default {
    name: "BeachCard",
    props: {
        item: {
            type: Object,
            required: true,
        },
        index: {
            type: Number,
            required: true,
        },
    },
    components: {
        ZoneCard,
    },
    data() {
        return {
            expanded: false,
        };
    },
    methods: {
        toggleExpansion() {
            this.expanded = !this.expanded;
        },
    },
};
</script>
  