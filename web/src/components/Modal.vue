<template>
  <teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center z-50">
      <div class="fixed inset-0 bg-slate-800 opacity-50 mix-blend-difference"></div>
      <div class="modal max-w-md p-3 min-w-[400px] bg-white dark:bg-black rounded-lg shadow-md relative">
        <div class="modal-header flex text-white ">
          <div class="flex-1">
            <h2 class="text-xl text-black dark:text-white"> <b></b>{{ item.name }}</h2>
          </div>
          <div class="flex-none">
            <button @click="closeModal" class="text-black text-lg dark:text-white focus:outline-none">&times;</button>
          </div>
        </div>
        <div class="modal-content pt-4 ">
          <!-- Contenuto della modal -->
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
    closeModal() {
      // Emetti un evento per chiudere la modal
      this.$emit('close-modal');
    },
  }
}
</script>
