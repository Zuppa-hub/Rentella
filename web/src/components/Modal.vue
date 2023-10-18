<template>
  <teleport to='body'>
    <div class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ title }}</h2>
          <button @click="closeModal">Chiudi</button>
        </div>
        <div class="modal-content">
          <!-- Contenuto della modal -->
        </div>
      </div>
    </div>
  </teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  /* Sfondo trasparente */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  /* Valore di z-index elevato */
}

.modal {
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  width: 70%;
  /* Larghezza personalizzabile */
  max-width: 400px;
  /* Larghezza massima personalizzabile */
  padding: 20px;
  text-align: center;
  z-index: 1001;
  /* Valore di z-index superiore a modal-overlay */
}

/* Altri stili CSS per personalizzare ulteriormente la modal */
</style>



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
