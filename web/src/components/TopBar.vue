<template>
    <header class="sticky top-0 ">
        <nav class="bg-primary duration-100">
            <div class="w-full flex items-center justify-between p-4">
                <svg height="40" class=" bg-LogoDark object-left " style="background-repeat: no-repeat;">
                </svg>
                <hr class="border-b border-gray-300 dark:border-gray-600 mb-2" />
                <div class="sm:flex sticky top-0 ml-4 md:hidden">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                        <span class="text-primary-700 text-xl font-semibold">{{ profileLetter }}</span>
                        <!-- La lettera del profilo -->
                    </div>
                </div>
                <div class="hidden md:flex justify-end items-center object-left">
                    <ul class="flex font-medium flex-row space-x-8 object-left">
                        <RouterLink :to="{ name: 'Home' }">
                            <li>
                                <a class="flex py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150 items-center"
                                    aria-current="page">
                                    <svg height="24" width="24" class="bg-HomeIcon mx-2"
                                        style="background-repeat: no-repeat;">
                                    </svg>Home
                                </a>
                            </li>
                        </RouterLink>
                        <RouterLink :to="{ name: 'Order' }">
                            <li>
                                <a href="#"
                                    class=" py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700  dark:text-white hover:text-gray-300 duration-150 flex items-center"
                                    aria-current="page">
                                    <svg height="24" width="24" class="bg-ActiveIcon mx-2"
                                        style="background-repeat: no-repeat;">
                                    </svg>Active
                                </a>
                            </li>
                        </RouterLink>
                        <RouterLink :to="{ name: 'History' }">
                            <li>
                                <a href="#"
                                    class="flex py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150 items-center"
                                    aria-current="page">
                                    <svg height="24" width="24" class="bg-HistoryIcon mx-2"
                                        style="background-repeat: no-repeat;">
                                    </svg>History
                                </a>
                            </li>
                        </RouterLink>
                        <RouterLink :to="{ name: 'Home' }">
                            <li>
                                <a href="#"
                                    class="flex py-2 pr-4 pl-3 text-white rounded bg-primary-700 bg-transparent text-primary-700  dark:text-white hover:text-gray-300 duration-150 items-center"
                                    aria-current="page">
                                    <svg height="24" width="24" class="bg-SettingsIcon mx-2"
                                        style="background-repeat: no-repeat;">
                                    </svg>Settings
                                </a>
                            </li>
                        </RouterLink>
                        <div class="sm:flex relative ml-4">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                                <span class="text-primary-700 text-xl font-semibold">{{ profileLetter }}</span>
                                <!-- La lettera del profilo -->
                            </div>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
</template>
<script lang="ts">
import { RouterLink } from "vue-router";
import KeyCloakService from "../KeycloakService";

export default {
    name: "topbar",
    data() {
        return {
            profileLetter: "",
        };
    },
    created() {
        // Utilizza il metodo GetUserName() di KeyCloakService per ottenere il nome utente
        const userName = KeyCloakService.GetUserName();
        // Imposta il valore di profileLetter in base all'esistenza del nome utente
        this.profileLetter = userName ? userName[0].toUpperCase() : "";
    },
};

</script>
