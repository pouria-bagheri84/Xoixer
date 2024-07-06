<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
  group: Object
});

</script>

<template>
  <div class="mb-4 hover:bg-gray-100 cursor-pointer">
    <Link :href="route('group.profile', group.slug)" class="flex items-start gap-1 py-2 px-2">
      <img :src="group.thumbnail_url" alt="" class="w-[32px] h-[32px] rounded-full">
      <div class="flex-1">
        <div class="flex justify-between">
          <h3 class="font-bold text-lg">{{ group.name }}</h3>
          <span
              v-if=" group.status === 'approved' && group.role === 'admin'"
              class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
            {{group.role}}
          </span>
          <span
              v-else
              class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">>
            not approved
          </span>
<!--          {{ group.status === "approved" ? (group.role === "admin" ? group.role : "") : "not approved" }}-->
        </div>
        <div class="text-xs text-gray-500">{{ group.description }}</div>
      </div>
    </Link>
  </div>
</template>

<style scoped>

</style>