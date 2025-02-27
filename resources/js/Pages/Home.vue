<script setup lang="ts">
import {ref} from "vue";

const counter = ref(0);

const increment = () => {
    counter.value++;
};

const decrement = () => {
    counter.value--;
};
</script>

<template>
    <div class="app-container">
        <div class="counter-display">{{ counter }}</div>
        <div class="button-container">
            <button @click="decrement">-</button>
            <button @click="increment">+</button>
        </div>
    </div>
</template>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    font-family: Arial, sans-serif;
    background-color: #f0f4f8;
    color: #333;
}

h1 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.counter-display {
    font-size: 1.5rem;
    margin: 1rem 0;
}

.button-container {
    display: flex;
    gap: 1rem;
}
</style>
