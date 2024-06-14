<script setup>
import { ArrowDownTrayIcon, PaperClipIcon } from "@heroicons/vue/24/outline"
import {isImage} from "@/helpers.js";

defineProps({
  attachments: Array,
})

defineEmits(['attachmentsClick'])
</script>

<template>
  <template v-for="(attachment, index) of attachments.slice(0,4)">
    <div @click="$emit('attachmentsClick', index)"
         class="group aspect-square bg-indigo-50 text-gray-500 flex flex-col items-center justify-center relative cursor-pointer">
      <!--          Download btn-->
      <a @click.stop :href="route('post.download', attachment)"
         class="z-20 opacity-0 group-hover:opacity-100 transition-all absolute right-2 top-2 cursor-pointer bg-gray-600 hover:bg-gray-800 text-gray-100 flex items-center justify-center w-8 h-8 rounded">
        <ArrowDownTrayIcon class="w-5 h-5"/>
      </a>
      <!--          /Download btn-->
      <div v-if="index === 3 && attachments.length > 4"
           class="absolute left-0 top-0 right-0 bottom-0 z-10 text-2xl bg-black/50 text-white flex items-center justify-center">
        + {{attachments.length - 4}} more...
      </div>
      <img v-if="isImage(attachment)"
           :src="attachment.url"
           alt=""
           class="object-contain aspect-square">

      <div v-else class="flex flex-col justify-center items-center">
        <PaperClipIcon class="w-11 h-11"/>
        <small class="text-center">{{ attachment.name }}</small>
      </div>
    </div>
  </template>
</template>
