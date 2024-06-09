<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "./PostModal.vue"
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import AttachmentPreviewPostModal from "@/Components/app/AttachmentPreviewPostModal.vue";

defineProps({
  posts: Object
})
const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const editPost = ref({})
const previewAttachmentsPost = ref({})
const authUser = usePage().props.auth.user;

function openEditModal(post){
  editPost.value = post
  showEditModal.value = true
}

function openAttachmentPreviewModal(post, index) {
  previewAttachmentsPost.value = {
    post,
    index
  }
  showAttachmentsModal.value = true;
}

function onModalHide() {
  editPost.value = {
    id: null,
    body: '',
    user: authUser
  }
}
</script>


<template>
  <div class="overflow-y-auto">

    <PostItem v-for="post of posts" :key="post.id" :post="post"
              @editClick="openEditModal"
              @attachmentClick="openAttachmentPreviewModal"
    />

    <PostModal @hide="onModalHide" :post="editPost" v-model="showEditModal"/>

    <AttachmentPreviewPostModal :attachments="previewAttachmentsPost.post?.attachments || []"
                                v-model:index="previewAttachmentsPost.index"
                                v-model="showAttachmentsModal"/>
  </div>

</template>


<style scoped>

</style>