<template>
    <div>
        <RouterView></RouterView>
        <Toaster></Toaster>
    </div>
</template>

<script setup>
import Toaster from "./components/ui/Toaster.vue";
</script>

<style lang="css">
html {
    scroll-behavior: smooth;
}
</style>
