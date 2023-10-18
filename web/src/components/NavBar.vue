<template>
    <footer class="sticky md:hidden">
        <nav class="bg-primary p-4 text-white overflow-x-auto">
            <div class="grid h-full max-w-lg grid-cols-4 mx-auto space-x-auto font-medium">
                <RouterLink :to="{ name: 'Home' }">
                    <div class="inline-flex flex-col items-center justify-center px-5 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150 "
                        aria-current="page">
                        <svg height="24" width="24" class="bg-HomeIcon mx-2" style="background-repeat: no-repeat;">
                        </svg>Home
                    </div>
                </RouterLink>
                <RouterLink :to="{ name: 'Order' }">
                    <div class="inline-flex flex-col items-center justify-center px-5 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150"
                        aria-current="page">
                        <svg height="24" width="24" class="bg-ActiveIcon mx-2" style="background-repeat: no-repeat;">
                        </svg>Active
                    </div>
                </RouterLink>
                <RouterLink :to="{ name: 'History' }">
                    <div class="inline-flex flex-col items-center justify-center px-5 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150 "
                        aria-current="page">
                        <svg height="24" width="24" class="bg-HistoryIcon mx-2" style="background-repeat: no-repeat;">
                        </svg>History
                    </div>
                </RouterLink>
                <RouterLink :to="{ name: 'Home' }">
                    <div class="inline-flex flex-col items-center justify-center px-5 text-white rounded bg-primary-700 bg-transparent text-primary-700 dark:text-white hover:text-gray-300 duration-150 "
                        aria-current="page">
                        <svg height="24" width="24" class="bg-SettingsIcon mx-2" style="background-repeat: no-repeat;">
                        </svg>Settings
                    </div>
                </RouterLink>
            </div>
        </nav>
    </footer>
</template>

  
<script lang="ts">
import { RouterLink } from "vue-router";

export default {
    name: "FooterBar",
    components: {
        RouterLink,
    },
};
</script>
  
<style scoped>
/* Assicurati di avere le classi CSS "bg-primary" e "text-white" definite nel tuo foglio di stile o nel tuo framework CSS */
</style>
  