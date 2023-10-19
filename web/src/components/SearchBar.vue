<template>
    <form class="py-3 sm:py-4 sticky top-0 dark:bg-gray-950 bg-white">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                </svg>
            </div>
            <input type="search" id="default-search"
                class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500"
                :value="searchTerm" @input="$emit('updateSearchTerm', $event.target.value)" :placeholder="'Search ' + name"
                required>

        </div>
    </form>
</template>

<script lang="ts">
export default {
    name: "SearchBar",
    props: {
        apiData: {
            type: Array,
            required: true,
        },
        searchTerm: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
    },
    computed: {
        searchTermComputed: {
            get() {
                return this.searchTerm;
            },
            set(value) {
                this.$emit('update:searchTerm', value);
            },
        },
    },
    created() {
        console.log(this.name);
    },
}
</script>