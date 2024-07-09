<script setup>

import PostItem from "@/Components/app/PostItem.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PostModal from "@/Components/app/PostModal.vue";
import AttachmentPreviewPostModal from "@/Components/app/AttachmentPreviewPostModal.vue"
import {ref} from "vue";
import {usePage, Head} from "@inertiajs/vue3";

defineProps({
  post: Object
})
const authUser = usePage().props.auth.user;

const showEditModal = ref(false)
const showAttachmentsModal = ref(false)
const editPost = ref({})
const previewAttachmentsPost = ref({})

function openEditModal(post) {
  editPost.value = post;
  showEditModal.value = true;
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
  <Head :title="'Post Page'"/>
  <AuthenticatedLayout>
    <div class="p-8 w-[600px] mx-auto h-full overflow-auto">
      <PostItem
          :post="post"
          @editClick="openEditModal"
          @attachmentClick="openAttachmentPreviewModal"/>
    </div>
  </AuthenticatedLayout>

  <PostModal @hide="onModalHide" :post="editPost" v-model="showEditModal"/>

  <AttachmentPreviewPostModal :attachments="previewAttachmentsPost.post?.attachments || []"
                              v-model:index="previewAttachmentsPost.index"
                              v-model="showAttachmentsModal"/>
</template>


<style scoped>

</style>