<script setup>
import {defineProps, onMounted, ref, watch} from 'vue';

const props = defineProps({
  placeholder: String,
  autoResize: {
    type: Boolean,
    default: true,
  }
})

const model = defineModel({
    type: String,
    required: true,
});

watch(() => props.modelValue, () => {
  console.log("Changed")
  setTimeout(() => {
    adjustHeight()
  }, 10)
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null);
onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
    adjustHeight()
});

defineExpose({ focus: () => input.value.focus() });

function onInputChange($event){
  emit('update:modelValue', $event.target.value)
  adjustHeight()
}

function adjustHeight(){
  if (props.autoResize){
    input.value.style.height = 'auto';
    input.value.style.height = (input.value.scrollHeight + 1) + 'px';
  }
}

</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 dark:text-slate-950 dark:bg-slate-200 focus:ring-indigo-500 rounded-md shadow-sm"
        v-model="model"
        @input="onInputChange"
        ref="input"
        :placeholder="placeholder"
    ></textarea>
</template>
