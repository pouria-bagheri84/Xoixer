<script setup>
import {defineProps, onMounted, ref} from 'vue';

defineProps({
  placeholder: String
})

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
        v-model="model"
        ref="input"
        :placeholder="placeholder"
    />
</template>
