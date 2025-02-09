<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: String,
    options: Array,
    valueKey: {
        type: String,
        default: 'id',
    },
    labelKey: {
        type: String,
        default: 'name',
    },
    placeholder: {
        type: String,
        default: 'Seçin',
    },
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <select
        ref="input"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option value="">{{placeholder}}</option>
        <option v-for="option in options" :key="option[valueKey]" :value="option[valueKey]">
            {{ option[labelKey] }}
        </option>
    </select>
</template>
