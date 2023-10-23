<template>
    <div class="dark:bg-gray-950 bg-white overflow-y-auto z-40 w-full" :class="{ 'rounded-corner': !roundedCornerFlag }">
        <div class="mx-4 items-start justify-start  dark:text-white">
            <SearchBar :apiData="apiData" :searchTerm="searchTerm" :name="searchBarTitle"
                @updateSearchTerm="updateSearchTerm" />
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
                <!-- This code is rendering a list of items using the `v-for` directive. It iterates over the
                `filterItems()` method, which returns a filtered array of items based on the `searchTerm` property. -->
                <li class="pb-4 mb-4" v-if="apiData.length == 0" v-for="number in 10" :key="number">
                    <SkeletonLoader class="w-full h-20" />
                </li>
                <li class="pb-4 mb-4" v-for="(item, index) in filterItems()" :key="index" @click="openModalForItem(item)">
                    <component :is="componentType" :item="item" :index="index + 1" />
                </li>
            </ul>
            <!--The code is conditionally rendering a `<bottomButton>` component based on the value of
            // the `bottomButtonShow` prop. -->
            <div v-if="bottomButtonShow">
                <bottomButton :title="bottomButtonTitle" :button="bottomButtonText"></bottomButton>
            </div>
        </div>
    </div>
    <div v-if="showModal" class="modal-overlay">
        <Modal :item="selectedItem" :data="selectedItem" :show="showModal" :contentComponet="ModalContentComponent"
            @close-modal="closeModal" />
    </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import LocationCard from './LocationCard.vue';
import OrderCard from './OrderCard.vue';
import SearchBar from './SearchBar.vue';
import Modal from './Modal.vue';
import bottomButton from './bottomButton.vue';
import SkeletonLoader from './SkeletonLoader.vue';

interface ApiDataItem {
    name: string;
}

export default defineComponent({
    name: "Sidebar",
    props: {
        apiData: {
            type: Array as () => ApiDataItem[], 
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
        componentType: {
            type: String,
            required: true,
        },
        roundedCornerFlag: {
            type: Boolean,
            required: false,
            default: false,
        },
        searchBarTitle: {
            type: String,
            required: true,
        },
        bottomButtonShow: {
            type: Boolean,
            required: false,
            default: false,
        },
        bottomButtonTitle: {
            type: String,
            required: false,
            default: null,
        },
        bottomButtonText: {
            type: String,
            required: false,
            default: null,
        },
        ModalContentComponent: {
            type: String,
            required: false,
            default: "",
        },
    },
    components: {
        SearchBar,
        LocationCard,
        OrderCard,
        Modal,
        bottomButton,
        SkeletonLoader,
    },
    data() {
        return {
            searchTerm: "",
            showModal: false,
            selectedItem: {} as ApiDataItem, // Inizializza come oggetto vuoto di tipo ApiDataItem
        };
    },
    emits: ['deleted'],
    methods: {
        test() {
            console.log("ciao");
        },
        updateSearchTerm(newSearchTerm: string) {
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
        openModalForItem(item: ApiDataItem) {
            this.selectedItem = item; // Memorizza i dettagli dell'elemento selezionato
            this.showModal = true; // Apri la modal
        },
        closeModal() {
            this.showModal = false;
        },
    },
});
</script>
