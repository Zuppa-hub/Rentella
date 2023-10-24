<template>
    <div class="scrollable-content">
        <modalInfoTile :title="'Distance'" :detail="item.distance"></modalInfoTile>
        <modalInfoTile :title="'Number of beaches:'" :detail="item.beach_count"></modalInfoTile>
        <modalInfoTile :title="'Price range'" :detail="item.min_price + ' - ' + item.max_price"></modalInfoTile>
    </div>
    <div class="flex dark:text-white mt-4">
        <div class="flex-1">
            <button @click="closeModal" class="w-full dark:text-white font-medium mt-2">Cancel</button>
        </div>
        <div class="flex-1">
            <router-link
                :to="{ name: 'BeachSelection', params: { id: item.id }, query: { cityName: item.name, distance: item.distance } }">
                <button class="w-full text-white bg-primary font-medium rounded-lg text-sm px-6 py-2.5">Select
                    location</button>
            </router-link>
        </div>
    </div>
</template>
  
<script lang="ts">
import modalInfoTile from './modalInfoTile.vue';
export default {
    name: "LocationModalDetail",
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
    components: {
        modalInfoTile,
    },
    emits: ['close-modal'],
    methods: {
        closeModal() {
            this.$emit('close-modal');
        },
    }
};
</script>
  