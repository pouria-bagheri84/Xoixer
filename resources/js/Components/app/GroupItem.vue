<script setup>
import {Link} from "@inertiajs/vue3";
import {ref} from "vue";

defineProps({
  group: Object
});

const status = ref('')

</script>

<template>
  <div class="mb-4 hover:bg-gray-100 cursor-pointer">
    <Link :href="route('group.profile', group.slug)" class="flex items-start gap-1 py-2 px-2">
      <img :src="group.thumbnail_url" alt="" class="w-[32px] h-[32px] rounded-full">
      <div class="flex-1">
        <div class="flex justify-between">
          <h3 class="font-bold text-lg">{{ group.name }}</h3>

          <span :class="{'bg-green-50 ring-green-600/20 text-green-700 inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium  ring-1 ring-inset' : status === 'admin', 'bg-yellow-50 ring-yellow-600/20 text-yellow-700 inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium  ring-1 ring-inset': status === 'not approved'}"
                v-if="group.status === 'approved' ? (group.role === 'admin' ? status = group.role : '') : status = 'not approved'">
            {{status}}
          </span>

<!--          {{ group.status === "approved" ? (group.role === "admin" ? group.role : "") : "not approved" }}-->
        </div>
<!--        <div class="text-xs text-gray-500" v-html="group.description"></div>-->
      </div>
    </Link>
  </div>
</template>

<style scoped>

</style>