<template>
  <div class="flex items-center gap-2">
    <a href="javascipt:void(0)">
      <img :src="post.user.avatar_url" alt="" class="w-[48px] h-[48px] rounded-full border border-2 transition-all hover:border-blue-700">
    </a>
    <div>
      <h4 class="font-bold">
        <a href="javascipt:void(0)" class="hover:underline">{{ post.user.name }}</a>
        <template v-if="post.group">
          >
          <a href="javascipt:void(0)" class="hover:underline">{{ post.group.name }}</a>
        </template>
      </h4>
      <small v-show="showTime" class="text-gray-400">{{ post.updated_at }}</small>
    </div>
  </div>
</template>

<script setup>
  defineProps({
    post: Object,
    showTime: {
      type: Boolean,
      default: true
    }
  })
</script>

<style scoped>

</style>