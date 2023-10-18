<template>
    <div class="dark:bg-gray-950 bg-white overflow-y-auto z-40 w-full" :class="{ 'rounded-corner': !roundedCornerFlag }">
        <div class="mx-4 items-start justify-start  dark:text-white">
            <SearchBar :apiData="apiData" :searchTerm="searchTerm" @updateSearchTerm="updateSearchTerm" />
            <div class="flex items-baseline my-3">
                <div class="flex-1">
                    <p class="text-xl font-bold">{{ title }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 dark:text-gray-400 text-right">{{ subtitle }} {{ filterItems().length }}
                    </p>
                </div>
            </div>
            <ul class="divide-gray-500 dark:divide-gray-700 flex flex-col">
                <li class="pb-4 mb-4" v-for="(item, index) in filterItems()" :key="index">
                    <component :is="componentType" :item="item" :index="index + 1" />
                </li>
            </ul>
        </div>
    </div>
</template>
<script lang="ts">
import LocationCard from './LocationCard.vue';
import OrderCard from './OrderCard.vue';
import SearchBar from './SearchBar.vue';
export default {
    name: "Sidebar",
    props: {
        apiData: {
            type: Array,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        subtitle: {
            type: String,
            required: true,
        },
        componentType: { // Prop per specificare il tipo di componente da renderizzare
            type: String,
            required: true,
        },
        roundedCornerFlag: {
            type: Boolean,
            required: false,
            default: false,
        }
    },
    components: {
        SearchBar,
        LocationCard,
        OrderCard
    },
    data() {
        return {
            searchTerm: "",
        }
    },
    methods: {
        updateSearchTerm(newSearchTerm) {
            this.searchTerm = newSearchTerm;
        },
        filterItems() {
            if (!this.apiData || !this.searchTerm) {
                return this.apiData;
            }
            const searchTermLower = this.searchTerm.toLowerCase();

            return this.apiData.filter(item => {
                return item.name.toLowerCase().includes(searchTermLower);
            });
        },
    }
}
</script>