<script setup>
import TextInput from "../TextInput.vue"
import {ref} from "vue";
import GroupItem from "./GroupItem.vue"

const searchKeywords = ref('')
</script>

<template>
  <TextInput :model-value="searchKeywords" placeholder="Type tp search..." class="w-full mt-4"/>
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
</template>


<style scoped>

</style>