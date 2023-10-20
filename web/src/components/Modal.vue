<template>
  <teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center z-50">
      <div class="fixed inset-0 bg-slate-900 opacity-50 mix-blend-difference"></div>
      <div class="modal max-w-md p-3 min-w-[400px] bg-white dark:bg-black rounded-lg shadow-md relative">
        <div class="modal-header flex text-white ">
          <div class="flex-1">
            <h2 class="text-2xl text-black dark:text-white"> <b>{{ item.name }} </b> </h2>
          </div>
          <div class="flex-none">
            <button @click="closeModal" class="text-black text-lg dark:text-white focus:outline-none">&times;</button>
          </div>
        </div>
        <hr class="border-b border-gray-300 dark:border-gray-600 mt-4" />
        <div class="modal-content pt-4">
          <!-- Modal content -->
          <!--The line `<component :is="contentComponet" :item="item" @close-modal="closeModal" />` 
            is dynamically rendering a component based on the value of the `contentComponet` prop. -->
          <component :is="contentComponet" :item="item" @close-modal="closeModal" />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
import OrderModalDetail from './OrderModalDetail.vue';
import LocationModalDetail from './LocationModalDetail.vue';
export default {
  name: "Modal",
  components: {
    OrderModalDetail,
    LocationModalDetail,
  },
  props: {
    contentComponet: { // Prop per specificare il tipo di componente da renderizzare
      type: String,
      required: true,
    },
    item: {
      type: Object,
      required: false,
      default: []
    }
  },
  emits: ['close-modal'],
  methods: {
    // The `closeModal()` method is emitting a custom event called 'close-modal'. This allows the parent
    // component to listen for this event and perform any necessary actions when the modal is closed. By
    // emitting this event, the parent component can handle the logic to close the modal or perform any
    // other desired actions.
    closeModal() {
      this.$emit('close-modal');
    },
  }
}
</script>
