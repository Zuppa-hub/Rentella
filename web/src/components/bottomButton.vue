<template>
    <div class="text-center">
        <p><b>{{ title }}</b></p>
        <router-link to="History"><u>{{ button }}</u></router-link>
    </div>
</template>
  
<script lang="ts">
export default {
    name: "BottomButton",
    props: {
        title: {
            type: String,
            required: true,
        },
        button: {
            type: String,
            required: true,
        }
    },
}
</script>
  