<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {ChatBubbleLeftRightIcon, HandThumbUpIcon} from '@heroicons/vue/24/outline'
import PostUserHeader from './PostUserHeader.vue'
import {router} from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from './ReadMoreReadLess.vue'
import EditDeleteDropdown from './EditDeleteDropdown.vue'
import PostAttachments from "@/Components/app/PostAttachments.vue";
import CommentList from "@/Components/app/CommentList.vue";

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

function sendPostReaction() {
  axiosClient.post(route('post.reaction', props.post), {
    reaction: 'like'
  })
      .then(({data}) => {
        props.post.current_user_has_reaction = data.current_user_has_reaction;
        props.post.num_of_reactions = data.num_of_reactions;
      })
}
</script>


<template>
  <div class="bg-white border rounded p-4 shadow mb-4">
    <div class="flex items-center justify-between">
      <PostUserHeader :post="post"/>
      <EditDeleteDropdown :user="post.user"
                          :post="post"
                          @edit="openEditModal"
                          @delete="deletePost"/>
    </div>
    <div>
      <ReadMoreReadLess :content="post.body" />
    </div>
    <div class="grid gap-3 mb-4" :class="[post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
      <PostAttachments :attachments="post.attachments" @attachmentsClick="openAttachment"/>
    </div>
    <div>
      <Disclosure v-slot="{ open }">
        <div class="flex gap-2">
          <button
              @click="sendPostReaction"
              class="text-gray-800 flex gap-1 items-center justify-center  rounded-lg py-2 px-4 flex-1"
              :class="[
                    post.current_user_has_reaction ?
                     'bg-sky-200 hover:bg-sky-300' :
                     'bg-gray-100  hover:bg-gray-200'
                ]">
            <HandThumbUpIcon class="w-5 h-5"/>
            <span class="mr-2">{{ post.num_of_reactions }}</span>
            {{ post.current_user_has_reaction ? 'Unlike' : 'Like' }}
          </button>
          <DisclosureButton class="text-gray-800 flex gap-1 items-center justify-center bg-gray-100 rounded-lg hover:bg-gray-200 py-2 px-4 flex-1">
            <ChatBubbleLeftRightIcon class="w-5 h-5"/>
            <span class="mr-2">{{ post.num_of_comments }}</span>
            Comment
          </DisclosureButton>
        </div>

        <DisclosurePanel class="comment-list mt-3 max-h-[300px] overflow-auto pr-1">
          <CommentList :data="{comments: post.comments}" :post="post"/>
        </DisclosurePanel>
      </Disclosure>

    </div>
  </div>
</template>


