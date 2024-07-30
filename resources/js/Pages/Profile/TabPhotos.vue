<script setup>
import {ArrowDownTrayIcon, PaperClipIcon} from "@heroicons/vue/24/outline"
import AttachmentPreviewPostModal from "@/Components/app/AttachmentPreviewPostModal.vue"
import {isImage} from "@/helpers.js";
import {ref} from "vue";

defineProps({
  photos: Array,
})

const currentPhotoIndex = ref(0)
const showModal = ref(false)

function openPhoto(index) {
  currentPhotoIndex.value = index
  showModal.value = true
}
</script>

<template>
  <div class="grid gap-2 grid-cols-3 sm:grid-cols-5">
    <template v-for="(photo, index) of photos">
      <div @click="openPhoto(index)"
           class="group aspect-square bg-indigo-50 text-gray-500 flex flex-col items-center justify-center relative cursor-pointer rounded-lg">
        <!--          Download btn-->
        <a @click.stop :href="route('post.download', photo)"
           class="z-20 opacity-0 group-hover:opacity-100 transition-all absolute right-2 top-2 cursor-pointer bg-gray-600 hover:bg-gray-800 text-gray-100 flex items-center justify-center w-8 h-8 rounded">
          <ArrowDownTrayIcon class="w-5 h-5"/>
        </a>
        <!--          /Download btn-->

        <img v-if="isImage(photo)"
             :src="photo.url"
             alt=""
             class="object-contain aspect-square rounded-lg">

        <div v-else class="flex flex-col justify-center items-center">
          <PaperClipIcon class="w-11 h-11"/>
          <small class="text-center">{{ photo.name }}</small>
        </div>
      </div>
    </template>
  </div>

  <div v-if="!photos.length" class="py-8 text-center text-gray-600">
    There are no photo
  </div>

  <AttachmentPreviewPostModal :attachments="photos || []"
                              v-model:index="currentPhotoIndex"
                              v-model="showModal"/>
</template>

<style scoped>

</style>