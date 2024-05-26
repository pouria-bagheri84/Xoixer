<script setup>
import TextInput from "../TextInput.vue"
import {ref} from "vue";
import FollowingItem from "./FollowingItem.vue"

const searchKeywords = ref('')
</script>

<template>
  <TextInput :model-value="searchKeywords" placeholder="Type to search..." class="w-full mt-4"/>
  <div class="overflow-y-auto h-[200px] lg:flex-1 mt-3">
    <div v-if="false" class="text-gray-400 text-center p-3">
      You dont have any friends yet.
    </div>
    <div v-else>
      <FollowingItem
          image="https://picsum.photos/100"
          title="john doe"
      />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      /><FollowingItem
        image="https://picsum.photos/100"
        title="john doe"
    />

      <FollowingItem
          image="https://picsum.photos/100"
          title="elon musk"
      />
    </div>
  </div>
</template>

<style scoped>

</style>