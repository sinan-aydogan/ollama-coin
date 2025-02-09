<script setup>
import {computed, ref} from 'vue';

const emit = defineEmits(['update:checked']);
const input = ref(null);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        default: false,
    },
    value: {
        type: String,
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <div class="relative cursor-pointer" @click="input.click()">
        <input id="toggle" v-model="proxyChecked" type="checkbox" class="sr-only peer" ref="input">
        <div class="block w-14 h-8 bg-gray-300 rounded-full peer-checked:bg-blue-500 transition"></div>
        <div class="absolute left-1 top-1 w-6 h-6 bg-white rounded-full shadow-md transform peer-checked:translate-x-6 transition"></div>
    </div>
</template>

<style scoped>

</style>
