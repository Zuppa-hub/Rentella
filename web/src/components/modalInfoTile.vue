<template>
    <div class="flex dark:text-white">
        <div class="flex-1">
            <p>{{ title }}:</p>
        </div>
        <div class="flex-none">
            <p>{{ detail }}</p>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "modalInfoTile",
    props: {
        title: {
            type: String,
            required: true,
        },
        detail: {
            type: String,
            required: true,
        },
    }
}
</script>