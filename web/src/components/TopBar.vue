<template>
    <header class="sticky">
        <nav class="bg-primary duration-100">
            <div class="w-full flex flex-wrap items-center justify-between p-4">
                <svg height="40" class=" bg-LogoDark object-left " style="background-repeat: no-repeat;">
                </svg>
                <hr class="border-b border-gray-300 dark:border-gray-600 mb-2" />
                <div class="hidden md:flex justify-end items-center object-left">
                    <ul class="flex font-medium flex-row space-x-8 object-left">
                        <RouterLink :to="{ name:'Home'}">
                            <li>
                                <a class="block py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150"
                                    aria-current="page">Home</a>
                            </li>
                        </RouterLink>

                        <li>
                            <a href="#"
                                class="block py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700  dark:text-white hover:text-gray-300 duration-150"
                                aria-current="page">Active</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150"
                                aria-current="page">History</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700  dark:text-white hover:text-gray-300 duration-150"
                                aria-current="page">Settings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>
<script lang="ts">
import { RouterLink } from "vue-router"
export default {
    name: "topbar",
}
</script>
