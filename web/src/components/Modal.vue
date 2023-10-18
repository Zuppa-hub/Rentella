<template>
  <teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center z-50">
      <!-- Aggiungi una patina semitrasparente sotto la modal -->
      <div class="fixed inset-0 bg-slate-800 opacity-50 mix-blend-difference"></div>
      <div class="modal max-w-md p-6 bg-white dark:bg-black rounded-lg shadow-md relative">
        <div class="modal-header flex text-white p-4 ">
          <h2 class="text-xl text-black dark:text-white">{{ title }}</h2>
          <button @click="closeModal" class="text-black text-lg dark:text-white focus:outline-none">&times;</button>
        </div>
        <div class="modal-content p-4">
          <!-- Contenuto della modal -->
        </div>
      </div>
    </div>
  </teleport>
</template>

<script lang="ts">
export default {
  name: "Modal",
  props: {
    title: String
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
