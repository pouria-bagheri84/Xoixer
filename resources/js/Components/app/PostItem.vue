<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { PencilIcon, TrashIcon, EllipsisVerticalIcon, ArrowDownTrayIcon, PaperClipIcon, HandThumbUpIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline'
import PostUserHeader from './PostUserHeader.vue'
import {router} from "@inertiajs/vue3";
import {isImage} from "@/helpers.js";

const props = defineProps({
  post: Object
})

const emit = defineEmits(['editClick', 'attachmentClick'])

function openEditModal(){
  emit('editClick', props.post)
}

function deletePost(){
  if (window.confirm("are you sure you want to delete this post?")){
    router.delete(route('post.destroy', props.post),{
      preserveScroll: true
    })
  }
}

function openAttachment(ind){
  emit('attachmentClick', props.post, ind)
}
</script>


<template>
  <div class="bg-white border rounded p-4 shadow mb-4">
    <div class="flex items-center justify-between">
      <PostUserHeader :post="post"/>
      <div class="justify-self-end">
        <Menu as="div" class="relative inline-block text-left">
            <div>
              <MenuButton class="flex items-center justify-center w-8 h-8 rounded-full transition hover:bg-black/5">
                <EllipsisVerticalIcon class="w-5 h-5"/>
              </MenuButton>
            </div>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
              <MenuItems
                  class="z-30 absolute right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none"
              >
                <div class="px-1 py-1">
                  <MenuItem v-slot="{ active }">
                    <button
                        @click="openEditModal"
                        :class="[
                        active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                    >
                      <PencilIcon
                          class="mr-2 h-5 w-5"
                          aria-hidden="true"
                      />
                      Edit
                    </button>
                  </MenuItem>
                </div>
                <div class="px-1 py-1">
                  <MenuItem v-slot="{ active }">
                    <button
                        @click="deletePost"
                        :class="[
                        active ? 'bg-indigo-500 text-white' : 'text-gray-900',
                        'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                      ]"
                    >
                      <TrashIcon
                          class="mr-2 h-5 w-5"
                          aria-hidden="true"
                      />
                      Delete
                    </button>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
      </div>
    </div>
    <div>
      <Disclosure v-slot="{ open }">
        <div class="ck-content-output" v-if="!open" v-html="post.body.substring(0, 200)"></div>
        <template v-if="post.body.length > 200">
          <DisclosurePanel>
            <div class="ck-content-output" v-html="post.body"></div>
          </DisclosurePanel>

          <div class="flex justify-end">
            <DisclosureButton class="text-blue-600 hover:underline mb-4">
              {{ open ? "Read Less" : "Read More"}}
            </DisclosureButton>
          </div>
        </template>
      </Disclosure>
    </div>
    <div class="grid gap-3 mb-4" :class="[post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
      <template v-for="(attachment, index) of post.attachments.slice(0,4)">
        <div @click="openAttachment(index)" class="group aspect-square bg-indigo-50 text-gray-500 flex flex-col items-center justify-center relative cursor-pointer">
<!--          Download btn-->
          <a @click.stop :href="route('post.download', attachment)" class="z-20 opacity-0 group-hover:opacity-100 transition-all absolute right-2 top-2 cursor-pointer bg-gray-600 hover:bg-gray-800 text-gray-100 flex items-center justify-center w-8 h-8 rounded">
            <ArrowDownTrayIcon class="w-5 h-5"/>
          </a>
<!--          /Download btn-->
          <div v-if="index === 3 && post.attachments.length > 4" class="absolute left-0 top-0 right-0 bottom-0 z-10 text-2xl bg-black/50 text-white flex items-center justify-center">
            + {{post.attachments.length - 4}} more...
          </div>
          <img v-if="isImage(attachment)"
               :src="attachment.url"
               alt=""
               class="object-contain aspect-square">

          <div v-else class="flex flex-col justify-center items-center">
            <PaperClipIcon class="w-11 h-11"/>
            <small>{{ attachment.name }}</small>
          </div>
        </div>
      </template>
    </div>
    <div>
      <div class="flex gap-2">
        <button class="flex gap-1 items-center py-2 px-4 flex-1 justify-center bg-gray-100 hover:bg-gray-200 rounded-lg">
          <HandThumbUpIcon class="w-5 h-5"/>
          Like
        </button>
        <button class="flex gap-1 items-center py-2 px-4 flex-1 justify-center bg-gray-100 hover:bg-gray-200 rounded-lg">
          <ChatBubbleLeftRightIcon class="w-5 h-5"/>
          Comment
        </button>
      </div>
    </div>
  </div>
</template>


<style scoped>

</style>