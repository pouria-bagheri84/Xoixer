<script setup>
defineProps({
  image: String,
  title: String,
});

</script>

<template>
  <div class="mb-4 hover:bg-gray-100 cursor-pointer">
    <div class="flex items-center gap-2 py-2 px-2">
      <img :src="image" alt="" class="w-[32px] rounded-full">
      <div>
        <h3 class="font-bold">{{ title }}</h3>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>