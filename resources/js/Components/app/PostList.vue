<script setup>
import PostItem from "@/Components/app/PostItem.vue";
import PostModal from "./PostModal.vue"
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";

defineProps({
  posts: Object
})
const showEditModal = ref(false)
const editPost = ref({})
const authUser = usePage().props.auth.user;

function openEditModal(post){
  editPost.value = post
  showEditModal.value = true
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
    <PostItem v-for="post of posts" :key="post.id" :post="post" @editClick="openEditModal"/>
<!--    <PostItem :post="post2" />-->
    <PostModal @hide="onModalHide" :post="editPost" v-model="showEditModal"/>
  </div>

</template>


<style scoped>

</style>