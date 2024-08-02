<script setup>
import FollowingListItems from "@/Components/app/FollowingListItems.vue";
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

defineProps({
  users: Array
})
</script>


<template>
  <div class="px-3 bg-white rounded border py-3 overflow-hidden h-full dark:bg-slate-950 dark:border-slate-900 dark:text-gray-100">
    <div class="block lg:hidden">
      <Disclosure v-slot="{ open }">
        <DisclosureButton class="w-full">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold dark:text-slate-300">My Following</h2>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6 transition-all" :class=" open ? 'rotate-90 transform' : ''">
              <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" clip-rule="evenodd" />
            </svg>
          </div>
        </DisclosureButton>

        <DisclosurePanel>
          <FollowingListItems :users="users"/>
        </DisclosurePanel>
      </Disclosure>
    </div>

    <div class="lg:flex flex-col hidden overflow-hidden h-full">
      <h2 class="text-xl font-bold dark:text-slate-300">My Following</h2>
      <FollowingListItems :users="users"/>
    </div>
  </div>
</template>


<style scoped>

</style>