<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue'
import {ChatBubbleLeftRightIcon, HandThumbUpIcon, MapPinIcon} from '@heroicons/vue/24/outline'
import PostUserHeader from './PostUserHeader.vue'
import {router, useForm, usePage} from "@inertiajs/vue3";
import axiosClient from "@/axiosClient.js";
import ReadMoreReadLess from './ReadMoreReadLess.vue'
import EditDeleteDropdown from './EditDeleteDropdown.vue'
import PostAttachments from "@/Components/app/PostAttachments.vue";
import CommentList from "@/Components/app/CommentList.vue";
import {computed} from "vue";

const props = defineProps({
  post: Object
})

const authUser = usePage().props.auth.user
const group = usePage().props.group

const emit = defineEmits(['editClick', 'attachmentClick'])

const postBody = computed(()=>
  props.post.body.replace(
      /(#\w+)(?![^<]*<\/a>)/g,
      (match, group)=>{
        const encodedGroup = encodeURIComponent(group);
        return  `
                <a href="/search/${encodedGroup}"
                class="inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium ring-1 ring-inset ring-blue-600/20 dark:bg-slate-500 dark:ring-slate-600/20"
                >${group}</a>
                `
      }
  )
)

const isPinned = computed(() => {
  if (group?.id){
    return group?.pinned_post_id === props.post.id
  }

  return authUser?.pinned_post_id === props.post.id
})

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

function pinUnpinPost() {
  const form = useForm({
    forGroup : group?.id
  })

  let isPinned = false
  if (group?.id) {
    isPinned = group?.pinned_post_id === props.post.id;
  } else {
    isPinned = authUser?.pinned_post_id === props.post.id;
  }

  form.post(route('post.pin.unpin', props.post.id), {
    preserveScroll: true,
    onSuccess: ()=>{
      if (group?.id) {
        group.pinned_post_id = isPinned ? null : props.post.id
      } else {
        authUser.pinned_post_id = isPinned ? null : props.post.id
      }
    }
  })
}

function openAttachment(ind){
  emit('attachmentClick', props.post, ind)
}

function sendPostReaction() {
  axiosClient.post(route('post.reaction', props.post), {
    reaction: 'like'
  })
      .then(({data}) => {
        props.post.current_user_has_reaction = data.current_user_has_reaction
        props.post.num_of_reactions = data.num_of_reactions;
      })
}
</script>


<template>
  <div class="bg-white border rounded p-4 shadow mb-4 dark:bg-slate-950 dark:text-gray-100" :class="[isPinned ? 'border-indigo-500 dark:border-blue-600 border-2' : 'dark:border-slate-900']">
    <div class="flex items-center justify-between">
      <PostUserHeader :post="post"/>
      <div class="flex items-center gap-2">
        <div v-if="isPinned" class="flex items-center text-xs">
          <MapPinIcon class="h-3 w-3" aria-hidden="true"/>
          pinned
        </div>
        <EditDeleteDropdown :user="post.user"
                            :post="post"
                            @edit="openEditModal"
                            @delete="deletePost"
                            @pin="pinUnpinPost"/>
      </div>
    </div>
    <div class="mb-3">
      <ReadMoreReadLess :content="postBody" />
    </div>
    <div class="grid gap-3 mb-4" :class="[post.attachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
      <PostAttachments :attachments="post.attachments" @attachmentsClick="openAttachment"/>
    </div>
    <div>
      <Disclosure v-slot="{ open }">
        <div class="flex gap-2">
          <button
              @click="sendPostReaction"
              class="text-gray-800 dark:text-gray-100 flex gap-1 items-center justify-center  rounded-lg py-2 px-4 flex-1"
              :class="[
                    post.current_user_has_reaction ?
                     'bg-sky-100 dark:bg-sky-900 hover:bg-sky-200 dark:hover:bg-sky-950' :
                     'bg-gray-100 dark:bg-slate-800 dark:bg-slate-900 dark:hover:bg-slate-800 '
                ]"
          >
            <HandThumbUpIcon class="w-5 h-5"/>
            <span class="mr-2">{{ post.num_of_reactions }}</span>
            {{ post.current_user_has_reaction ? 'Unlike' : 'Like' }}
          </button>
          <DisclosureButton class="text-gray-800 flex gap-1 items-center justify-center bg-gray-100 rounded-lg hover:bg-gray-200 py-2 px-4 flex-1 dark:text-gray-100 dark:bg-slate-900 dark:hover:bg-slate-800">
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


