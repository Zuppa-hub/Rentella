<template>
    <div class="dark:bg-gray-950 bg-white overflow-y-auto z-40 w-full h-full">
        <div class="mt-6 ml-4 dark:text-white with-vertical-bar">
            <div class="mt-6 ml-4 dark:text-white">
                <p class="text-2xl font-bold">{{ title }}</p>
                <div class="mt-2">
                    <div class="border-b border-gray-600"></div>
                </div>
                <div class="mt-6">
                    <div class="flex">
                        <div class="flex-1">
                            <p>
                                Total orders
                            </p>
                        </div>
                        <div class="flex-none">
                            <p>
                                {{ total }}
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-1">
                            <p>
                                Favourite beach
                            </p>
                        </div>
                        <div class="flex-none">
                            <p>
                                {{ favouriteBeach }}
                            </p>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="flex-1">
                            <p>
                                Favourite city
                            </p>
                        </div>
                        <div class="flex-none">
                            <p>
                                {{ favouriteLocation }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <bottomButton :title="bottomButtonTitle" :button="bottomButtonText"></bottomButton>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import BottomButton from './bottomButton.vue';

export default {
    name: "leftPanel",
    props: {
        title: {
            type: String,
            required: true
        },
        total: {
            type: Number,
            required: true
        },
        favouriteBeach: {
            type: String,
            required: false,
            default: "make an order to have more information"
        },
        // The `bottomButtonTitle` property is defining a prop for the component. It specifies that the
        // `bottomButtonTitle` prop should be a string and is not required, meaning it can be omitted when
        // using the component. The `default` property is set to `null`, meaning that if the
        // `bottomButtonTitle` prop is not provided, its default value will be `null`.
        bottomButtonTitle: {
            type: String,
            required: false,
            default: null,
        },
        favouriteLocation: {
            type: String,
            required: false,
            default: "make an order to have more information"
        },
        // The `bottomButtonText` property is defining a prop for the component. It specifies that the
        // `bottomButtonText` prop should be a string and is not required, meaning it can be omitted when using
        // the component. The `default` property is set to `null`, meaning that if the `bottomButtonText` prop
        // is not provided, its default value will be `null`.
        bottomButtonText: {
            type: String,
            required: false,
            default: null,
        },
    },
    components: { BottomButton }
}

</script>
<style>
.with-vertical-bar {
    border-right: 2px solid #000 dark;
    /* Puoi personalizzare il colore e lo stile della barra verticale qui */
    padding-right: 15px;
    /* Aggiunge un margine a sinistra della barra */
}
</style>
