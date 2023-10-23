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
            <button @click="selectedLocation"
                class="w-full text-white bg-primary font-medium rounded-lg text-sm px-6 py-2.5">Select
                location</button>
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
    emits: ['close-modal', 'selectLocation'],
    methods: {
        closeModal() {
            this.$emit('close-modal');
        },
        selectedLocation() { //capisci come implemetare 
            this.$emit('selectLocation', this.item.id);
        },
    }
};
</script>
  