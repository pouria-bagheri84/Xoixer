<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { ArrowDownTrayIcon, PaperClipIcon, HandThumbUpIcon, ChatBubbleLeftRightIcon } from '@heroicons/vue/24/outline'
import PostUserHeader from './PostUserHeader.vue'
import {router, usePage} from "@inertiajs/vue3";
import {isImage} from "@/helpers.js";
import axiosClient from "@/axiosClient.js";
import InputTextarea from "@/Components/InputTextarea.vue";
import IndigoButton from "@/Components/app/IndigoButton.vue";
import {ref} from "vue";
import ReadMoreReadLess from './ReadMoreReadLess.vue'
import EditDeleteDropdown from './EditDeleteDropdown.vue'
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
  post: Object
})

const newCommentText = ref('')

const editingComment = ref(null)

const emit = defineEmits(['editClick', 'attachmentClick'])

const authUser = usePage().props.auth.user

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

function sendReaction() {
  axiosClient.post(route('post.reaction', props.post), {
    reaction: 'like'
  })
      .then(({data}) => {
        props.post.current_user_has_reaction = data.current_user_has_reaction;
        props.post.num_of_reactions = data.num_of_reactions;
      })
}

function createComment() {
  axiosClient.post(route('post.comment.create', props.post), {
    comment: newCommentText
  })
      .then(({data}) => {
        newCommentText.value = ''
        props.post.comments.unshift(data)
        props.post.num_of_comments ++;
      })
}

function startCommentEdit(comment) {
  // console.log(comment.comment)
  editingComment.value = {
    id: comment.id,
    comment: comment.comment.replace(/<br\s*\/?>/gi, '\n')
  }
}

function deleteComment(comment) {
  if (!window.confirm('Are You Sure You Want To Delete This Comment?')){
    return false
  }
  axiosClient.delete(route('post.comment.delete', comment.id))
      .then(({data}) => {
        props.post.comments = props.post.comments.filter(c => c.id !== comment.id)
        props.post.num_of_comments --;
      })
}

function updateComment() {
  axiosClient.put(route('post.comment.update', editingComment.value.id), editingComment.value)
      .then(({data}) => {
        editingComment.value = null
        props.post.comments = props.post.comments.map((c)=> {
          if (c.id === data.id){
            return data
          }
          return c
        })
      })
}
</script>


<template>
  <div class="bg-white border rounded p-4 shadow mb-4">
    <div class="flex items-center justify-between">
      <PostUserHeader :post="post"/>
      <EditDeleteDropdown :user="post.user" @edit="openEditModal" @delete="deletePost"/>
    </div>
    <div>
      <ReadMoreReadLess :content="post.body" />
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
            <small class="text-center">{{ attachment.name }}</small>
          </div>
        </div>
      </template>
    </div>
    <div>
      <Disclosure v-slot="{ open }">
        <div class="flex gap-2">
          <button
              @click="sendReaction"
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

        <DisclosurePanel class="mt-3">
          <div class="flex gap-2 mb-3">
            <a href="javascript:void(0)">
              <img :src="authUser.avatar_url" class="w-[40px] h-[40px] rounded-full border border-2 transition-all hover:border-blue-500"/>
            </a>
            <div class="flex flex-1">
              <InputTextarea v-model="newCommentText" placeholder="Enter your comment here" rows="1" class="w-full max-h-[150px] resize-none rounded-r-none"></InputTextarea>
              <IndigoButton @click="createComment" class="rounded-l-none">Submit</IndigoButton>
            </div>
          </div>
          <div>
            <div v-for="comment of post.comments" :key="comment.id" class="mb-4">
              <div class="flex justify-between gap-2">
                <div class="flex gap-2">
                  <a href="javascript:void(0)" class="w-[45px] h-[45px]">
                    <img :src="comment.user.avatar_url" class="w-[45px] h-[45px] rounded-full border border-2 transition-all hover:border-blue-500"/>
                  </a>
                  <div>
                    <h4 class="font-bold">
                      <a href="javascript:void(0)" class="hover:underline">
                        {{ comment.user.name }}
                      </a>
                    </h4>
                    <small class="text-xs text-gray-400">{{ comment.updated_at }}</small>
                  </div>
                </div>
                <EditDeleteDropdown :user="comment.user" @edit="startCommentEdit(comment)" @delete="deleteComment(comment)"/>
              </div>
              <div v-if="editingComment && editingComment.id === comment.id" class="ml-12">
                <InputTextarea v-model="editingComment.comment" placeholder="Enter your comment here" rows="1" class="w-full max-h-[150px] resize-none"></InputTextarea>
                <div class="flex justify-end">
                  <DangerButton @click="editingComment = null" class="rounded-r-none">Cancel</DangerButton>
                  <IndigoButton @click="updateComment" class="rounded-l-none">Update</IndigoButton>
                </div>
              </div>
              <ReadMoreReadLess v-else :content="comment.comment" content-class="text-sm flex flex-1 ml-12" />
            </div>
          </div>
        </DisclosurePanel>
      </Disclosure>

    </div>
  </div>
</template>


<style scoped>

</style>