<template>
    <div class="flex dark:text-white">
        <div class="flex-1">
            <p>Distance:</p>
        </div>
        <div class="flex-none">
            {{ item.distance }}
        </div>
    </div>
    <div class="flex dark:text-white">
        <div class="flex-1">
            <p>Number of beaches:</p>
        </div>
        <div class="flex-none">
            {{ item.beach_count }}
        </div>
    </div>
    <div class="flex dark:text-white">
        <div class="flex-1">
            <p>Price range:</p>
        </div>
        <div class="flex-none">
            {{ item.min_price }} - {{ item.max_price }}
        </div>
    </div>
    <div class="flex dark:text-white mt-4">
        <div class="flex-1">
            <button class="w-full dark:text-white font-medium mt-2">Cancel</button>
        </div>
        <div class="flex-1">
            <button type="button" class="w-full text-white bg-primary font-medium rounded-lg text-sm px-6 py-2.5">Select
                location</button>
        </div>
    </div>
</template>
  
<script lang="ts">
export default {
    name: "LocationModalDetail",
    props: {
        item: {
            type: Object,
            required: true,
        },
    },
};
</script>
  