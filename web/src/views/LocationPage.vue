<template>
    <div>
      <h1>Dati dall'API</h1>
      <button @click="fetchData">Carica dati dall'API</button>
      <div v-if="data">
        <p><strong>Titolo:</strong> {{ data.title }}</p>
        <p><strong>Contenuto:</strong> {{ data.body }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        data: null,
      };
    },
    methods: {
      fetchData() {
        axios
          .get('URL_DELL_API') // Sostituisci 'URL_DELL_API' con l'URL effettivo dell'API
          .then(response => {
            this.data = response.data;
          })
          .catch(error => {
            console.error('Si è verificato un errore durante la richiesta API:', error);
          });
      },
    },
  };
  </script>
  
  <style scoped>
  /* Stili CSS specifici per questo componente, se necessario */
  </style>
  