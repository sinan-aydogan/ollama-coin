<script setup>
defineModel({
    prop: 'modelValue',
    event: 'update:modelValue',
});
</script>

<template>
    <div v-if="modelValue" class="flex justify-center items-center">
        <svg class="animate-spin h-10 w-10 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V2.5"></path>
        </svg>
    </div>

    <div v-else>
        <slot></slot>
    </div>
</template>

<style scoped>

</style>
