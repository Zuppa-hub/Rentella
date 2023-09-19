<script setup lang="ts">
import { ref } from 'vue';
const counter = ref(0);

function increment() {
  counter.value++;
}

function decrement() {
  counter.value--;
}

</script>

<template>
  <div id="app">
    <h1>Contatore: {{ counter }}</h1>
    <button @click="increment">Incrementa</button>
    <button @click="decrement">Decrementa</button>
  </div>
</template>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  text-align: center;
  margin-top: 60px;
}

button {
  font-size: 1em;
  margin: 10px;
}
</style>
