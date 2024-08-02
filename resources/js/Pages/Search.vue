<script setup>
import {Head} from '@inertiajs/vue3';
import UsersListItem from "@/Components/app/UsersListItem.vue"
import GroupItem from "@/Components/app/GroupItem.vue"
import PostList from "@/Components/app/PostList.vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"

const props = defineProps({
  search: String,
  users: Array,
  groups: Array,
  posts: Object
})
</script>

<template>
  <Head :title="'Search'"/>
  <AuthenticatedLayout>
    <div class="p-4 overflow-auto h-full">
      <div v-if="!search.startsWith('#')" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="shadow bg-white p-3 rounded-lg mb-3 dark:bg-slate-950">
          <h2 class="text-lg font-bold dark:text-slate-300">Users</h2>
          <div class="grid-cols-2">
            <UsersListItem v-if="users.length" v-for="user of users" :user="user" class="rounded-lg mb-1 border-slate-200"/>
            <div v-else class="py-8 text-center text-gray-500">
              No users were found.
            </div>
          </div>
        </div>
        <div class="shadow bg-white p-3 rounded-lg mb-3 dark:bg-slate-950">
          <h2 class="text-lg font-bold dark:text-slate-300">Groups</h2>
          <GroupItem v-if="groups.length" v-for="group of groups" :group="group" class="rounded-lg border-2 transition-all hover:border-indigo-500 mb-1"/>
          <div v-else class="py-8 text-center text-gray-500">
            No groups were found.
          </div>
        </div>
      </div>
      <div class="lg:mx-[500px]">
        <h2 class="text-lg font-bold dark:text-slate-300">Posts</h2>
        <PostList v-if="posts.data.length" :posts="posts.data" class="flex-1"/>
        <div v-else class="py-8 text-center text-gray-500">
          No posts were found.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>

</style>