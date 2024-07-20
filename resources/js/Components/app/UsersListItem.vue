<script setup>
import {Link} from "@inertiajs/vue3";

defineProps({
  user: Object,
  forApprove: {
    type: Boolean,
    default: false
  },
  showRoleDropdown: {
    type: Boolean,
    default: false
  },
  disableRoleDropdown: {
    type: Boolean,
    default: false
  }
});

defineEmits(['approve', 'reject', 'roleChange', 'delete'])

</script>

<template>
  <div class=" bg-white transition-all border-2 border-transparent hover:border-indigo-500">
    <div class="flex items-center gap-2 py-2 px-2">
      <Link :href="route('profile', user.username)">
        <img :src="user.avatar_url || '/img/xoixer-logo.png'" class="w-[32px] h-[32px] rounded-full"/>
      </Link>
      <div class="flex justify-between flex-1">
        <Link :href="route('profile', user.username)">
          <h3 class="font-black hover:underline">{{ user.name }}</h3>
        </Link>

        <div class="flex gap-1" v-if="forApprove">
          <button class="text-xs py-1 px-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"
                  @click.prevent.stop="$emit('approve', user)">
            approve
          </button>
          <button class="text-xs py-1 px-2 rounded bg-red-500 hover:bg-red-600 text-white"
                  @click.prevent.stop="$emit('reject', user)">
            reject
          </button>
        </div>

        <div v-else-if="showRoleDropdown" class="flex gap-1">
          <div class="mt-2">
            <select
                class="rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 max-w-xs text-sm leading-6"
                @change="$emit('roleChange', user, $event.target.value)"
                :disabled="disableRoleDropdown">
              <option :selected="user.role === 'admin'">admin</option>
              <option :selected="user.role === 'user'">user</option>
            </select>
            <button @click="$emit('delete', user)"
                    class="text-xs py-1.5 px-2 ml-3 rounded bg-gray-700 hover:bg-gray-800 text-white disabled:bg-gray-500"
                    :disabled="disableRoleDropdown">
              delete
            </button>
          </div>
        </div>
        <div v-else>
          <span v-if="disableRoleDropdown"
                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
            {{"owner"}}
          </span>
          <span v-else-if="user.role === 'admin'"
                class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
            {{"admin"}}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>