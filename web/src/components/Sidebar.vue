<template>
    <div class="dark:bg-gray-950 bg-white overflow-y-auto z-40 w-full " :class="{ 'rounded-corner': !roundedCornerFlag }">
        <div class="flex justify-center">
            <div v-if="isScreenMdOrLarger">
                {{ showSidebarOnMdOrBigger() }}
            </div>
            <button @click="toggleSidebar" class="md:hidden mb-2 dark:text-white px-4 py-2">
                <div class="flex items-center">
                    <p>{{ isSidebarHidden ? 'Show' : 'Hide' }}</p>
                    <span v-if="isSidebarHidden">
                        <svg height="9" width="24" class="bg-ArrowUpIcon dark:bg-ArrowUpDarkIcon ml-3"
                            style="background-repeat: no-repeat;"></svg>
                    </span>
                    <span v-else>
                        <svg height="9" width="24" class="bg-ArrowDownIcon dark:bg-ArrowDownDarkIcon ml-3"
                            style="background-repeat: no-repeat;"></svg>
                    </span>
                </div>
            </button>
        </div>

        <div class="mx-4 items-start justify-start  dark:text-white">
            <SearchBar :apiData="apiData" :searchTerm="searchTerm" :name="searchBarTitle"
                @updateSearchTerm="updateSearchTerm" />
            <div v-if="!isSidebarHidden" class="mx-4 items-start justify-start dark:text-white">
                <!-- ... Altri contenuti ... -->
                <div class="flex items-baseline my-3">
                    <div class="flex-1">
                        <p class="text-xl font-bold">{{ title }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-right">{{ subtitle }} {{
                            filterItems().length }}
                        </p>
                    </div>
                </div>
                <ul class="divide-gray-500 dark:divide-gray-700 flex flex-col">
                    <!-- This code is rendering a list of items using the `v-for` directive. It iterates over the
                `filterItems()` method, which returns a filtered array of items based on the `searchTerm` property. -->
                    <li class="pb-4 mb-4" v-if="apiData.length == 0" v-for="number in 10" :key="number">
                        <SkeletonLoader class="w-full h-20" />
                    </li>
                    <li class="pb-4 mb-4" v-for="(item, index) in filterItems()" :key="index"
                        @click="openModalForItem(item)">
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

// The `interface ApiDataItem` is defining the structure or shape of an object that represents an item
// in the `apiData` array. It specifies that each item should have a `name` property of type `string`.
// This interface is used to provide type checking and ensure that the `apiData` array contains objects
// with the required properties.
interface ApiDataItem {
    name: string;
}

export default defineComponent({
    name: "Sidebar",
    props: {
        // The `apiData` property is defining a prop for the component. It specifies that the `apiData`
        // prop should be an array of objects that match the shape defined by the `ApiDataItem` interface.
        // The `type` property is set to `Array as () => ApiDataItem[]` to provide type checking and ensure
        // that the prop is an array of `ApiDataItem` objects. The `required` property is set to `true`,
        // indicating that the `apiData` prop is required and must be provided when using the component.
        apiData: {
            type: Array as () => ApiDataItem[],
            required: true,
        },
        // The `title` property is defining a prop for the component. It specifies that the `title`
        // prop should be a string and is required, meaning it must be provided when using the
        // component. This prop is used to display the title of the sidebar component.
        title: {
            type: String,
            required: true,
        },
        // The `subtitle` property is defining a prop for the component. It specifies that the
        // `subtitle` prop should be a string and is required, meaning it must be provided when using
        // the component. This prop is used to display the subtitle of the sidebar component.
        subtitle: {
            type: String,
            required: true,
        },
        // The `componentType` property is defining a prop for the component. It specifies that the
        // `componentType` prop should be a string and is required, meaning it must be provided when
        // using the component. This prop is used to determine the type of component to render for each
        // item in the list. The value of the `componentType` prop is used in the `v-for` loop to
        // dynamically render the appropriate component based on the `componentType` prop value.
        componentType: {
            type: String,
            required: true,
        },
        // The `roundedCornerFlag` property is defining a prop for the component. It specifies that the
        // `roundedCornerFlag` prop should be a boolean and is not required, meaning it can be omitted when
        // using the component. The `default` property is set to `false`, meaning that if the
        // `roundedCornerFlag` prop is not provided, its default value will be `false`.
        roundedCornerFlag: {
            type: Boolean,
            required: false,
            default: false,
        },
        // The `searchBarTitle` property is defining a prop for the component. It specifies that the
        // `searchBarTitle` prop should be a string and is required, meaning it must be provided when using
        // the component. This prop is used to pass the title of the search bar component to the Sidebar
        // component.
        searchBarTitle: {
            type: String,
            required: true,
        },
        // The `bottomButtonShow` property is defining a prop for the component. It specifies that the
        // `bottomButtonShow` prop should be a boolean and is not required, meaning it can be omitted when
        // using the component. The `default` property is set to `false`, meaning that if the
        // `bottomButtonShow` prop is not provided, its default value will be `false`.
        bottomButtonShow: {
            type: Boolean,
            required: false,
            default: false,
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
        // The `bottomButtonText` property is defining a prop for the component. It specifies that the
        // `bottomButtonText` prop should be a string and is not required, meaning it can be omitted when using
        // the component. The `default` property is set to `null`, meaning that if the `bottomButtonText` prop
        // is not provided, its default value will be `null`.
        bottomButtonText: {
            type: String,
            required: false,
            default: null,
        },
        // The `ModalContentComponent` property is defining a prop for the component. It specifies that the
        // `ModalContentComponent` prop should be a string and is not required, meaning it can be omitted
        // when using the component. The `default` property is set to an empty string `""`, meaning that if
        // the `ModalContentComponent` prop is not provided, its default value will be an empty string.
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
            isSidebarHidden: false,
            isScreenMdOrLarger: false,

        };
    },
    created() {
        // check dimension on component start 
        this.checkScreenSize();
        // Add listner for window resize 
        window.addEventListener("resize", this.checkScreenSize);
    },
    destroyed() {
        // Remove listener when component is destroyed
        window.removeEventListener("resize", this.checkScreenSize);
    },
    methods: {
        //check if window size is more or equal than md 
        checkScreenSize() {
            this.isScreenMdOrLarger = window.innerWidth >= 768; // "md" in Tailwind CSS è 768px
        },

        // The `toggleSidebar()` method is a function that is called when the button with
        // `@click="toggleSidebar"` is clicked. It toggles the value of the `isSidebarHidden` data property
        // between `true` and `false`.
        toggleSidebar() {
            this.isSidebarHidden = !this.isSidebarHidden;
        },
        // The `updateSearchTerm` method is a function that takes a parameter `newSearchTerm` of type
        // `string`. It is called when the `@updateSearchTerm` event is emitted from the `SearchBar`
        // component.
        updateSearchTerm(newSearchTerm: string) {
            this.searchTerm = newSearchTerm;
        },
        // The `filterItems()` method is a function that filters the items in the `apiData` array based on
        // the `searchTerm` property.
        filterItems() {
            if (!this.apiData || !this.searchTerm) {
                return this.apiData;
            }
            const searchTermLower = this.searchTerm.toLowerCase();

            return this.apiData.filter(item => {
                return item.name.toLowerCase().includes(searchTermLower);
            });
        },
        // The `openModalForItem` method is a function that is called when a list item is clicked. It takes
        // an `item` parameter of type `ApiDataItem`, which represents the selected item from the list.
        openModalForItem(item: ApiDataItem) {
            this.selectedItem = item;
            this.showModal = true;
        },
        // The `closeModal()` method is a function that is called when the modal is closed. It sets the value
        // of the `showModal` data property to `false`, which hides the modal from being displayed on the
        // screen.
        closeModal() {
            this.showModal = false;
        },
        // The above code is defining a method called "showSidebarOnMdOrBigger" in a Vue component. This method
        // sets the value of the "isSidebarHidden" data property to false.
        showSidebarOnMdOrBigger() {
            this.isSidebarHidden = false;
        }
    },
});
</script>
