<script setup>
import TextInput from "../TextInput.vue"
import {ref} from "vue";
import GroupItem from "./GroupItem.vue"
import GroupModal from "./GroupModal.vue"

const showNewGroupModal = ref(false)
const searchKeywords = ref('')
</script>

<template>
  <div class="flex gap-2 mt-4">
    <TextInput :model-value="searchKeywords" placeholder="Type tp search..." class="w-full"/>
    <button @click="showNewGroupModal = true" class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white rounded py-1 px-2 w-[120px]">create new</button>
  </div>
  <div class="overflow-y-auto h-[200px] lg:flex-1 mt-3">
    <div v-if="false" class="text-gray-400 text-center p-3">
      You are not joined to any groups
    </div>
    <div v-else>
      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      /><GroupItem
        image="https://picsum.photos/100"
        title="Laravel Developers"
        description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
    />

      <GroupItem
          image="https://picsum.photos/100"
          title="Laravel Developers"
          description="Lorem ipsum dolor sit amet, consectetur adipisicing elit."
      />
    </div>
  </div>
  <GroupModal v-model="showNewGroupModal"/>
</template>


<style scoped>

</style>