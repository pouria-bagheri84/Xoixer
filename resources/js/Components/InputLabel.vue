<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="block font-medium text-sm text-gray-700 dark:text-slate-400">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
