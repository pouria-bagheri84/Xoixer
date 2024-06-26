<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head} from "@inertiajs/vue3";
defineProps({
  title: String,
  // body: String
})

</script>

<template>
  <Head title="Error"/>
  <AuthenticatedLayout>
    <div class="p-16 text-center">
      <h1 class="text-2xl">{{ title }}</h1>
<!--      <div>{{ body }}</div>-->
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>

</style>