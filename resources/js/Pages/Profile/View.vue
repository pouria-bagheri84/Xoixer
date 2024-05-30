<script setup>
import {computed, ref} from 'vue'
import {XMarkIcon, CheckCircleIcon} from '@heroicons/vue/24/solid'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import {useForm, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const imagesForm = useForm({
  avatar: null,
  cover: null,
})

let showNotification = ref(true);
const authUser = usePage().props.auth.user;

const props = defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
  user: {
    type: Object,
  },
  errors: String
});

const isMyProfile = computed(()=> {
  return authUser && authUser.id === props.user.id
})

const coverImageSrc = ref('')

function onCoverChange(event){
  imagesForm.cover = event.target.files[0]
  if (imagesForm.cover){
    const reader = new FileReader()
    reader.onload = ()=>{
      coverImageSrc.value = reader.result
    }
    reader.readAsDataURL(imagesForm.cover)
  }
}

function cancelCoverImage(){
  imagesForm.cover = null
  coverImageSrc.value = null
}

function submitCoverImage(){
  imagesForm.post('/profile/update-images', {
    onSuccess: (user)=> {
      cancelCoverImage()
      setTimeout(()=>{
        showNotification.value = false
      }, 3000)
    }
  })
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-[850px] mx-auto h-full overflow-auto">
      <div v-show="showNotification && status === 'cover-image-updated'" class="my-2 py-2 px-3 rounded font-medium text-sm bg-emerald-500 text-white">
        Your Cover Image Has Been Updated.
      </div>
      <div v-if="errors.cover" class="my-2 py-2 px-3 rounded font-medium text-sm bg-red-500 text-white">
        {{ errors.cover }}
      </div>
      <div class="group relative bg-white">
        <img class="w-full h-[200px] object-cover" :src="coverImageSrc || user.cover_url || '/img/cover.jpg'" alt="">
        <div class="absolute top-2 right-2 ">
          <button v-if="!coverImageSrc" class="py-1 px-2 bg-gray-50 hover:bg-gray-100 text-gray-500 text-xs flex items-center rounded opacity-0 transition-all group-hover:opacity-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3 mr-1">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
            </svg>
            Update Cover Image
            <input type="file" class="absolute top-0 left-0 bottom-0 right-0 opacity-0 cursor-pointer" @change="onCoverChange">
          </button>
          <div v-else class="flex gap-3 bg-white p-2 opacity-0 transition-all group-hover:opacity-100 rounded">
            <button @click="cancelCoverImage" class="py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-500 text-xs flex items-center rounded">
              <XMarkIcon class="h-3 w-3 mr-1"/>
              Cancel
            </button>
            <button @click="submitCoverImage" class="py-1 px-2 bg-gray-700 hover:bg-gray-900 text-gray-100 text-xs flex items-center rounded">
              <CheckCircleIcon class="h-3 w-3 mr-1"/>
              Save
            </button>
          </div>
        </div>
        <div class="flex">
          <img class="ml-[48px] h-[128px] w-[128px] -mt-[64px] rounded-full" src="https://sm.ign.com/ign_nordic/cover/a/avatar-gen/avatar-generations_prsz.jpg" alt="">
          <div class="flex justify-between items-center flex-1 p-4">
            <h2 class="font-bold text-lg">{{ user.name }}</h2>
            <PrimaryButton v-if="isMyProfile">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
              </svg>
              Edit Profile
            </PrimaryButton>
          </div>
        </div>
      </div>

      <div class="border-t">
        <TabGroup>
          <TabList class="flex bg-white">
            <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
              <TabItem text="About" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Posts" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Followers" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Followings" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Photos" :selected="selected"/>
            </Tab>
          </TabList>

          <TabPanels class="mt-2">
            <TabPanel v-if="isMyProfile" key="posts">
              <Edit :must-verify-email="mustVerifyEmail"/>
            </TabPanel>
            <TabPanel key="followers" class="bg-white p-3 shadow">
              Posts Content
            </TabPanel>
            <TabPanel key="followers" class="bg-white p-3 shadow">
              Followers Content
            </TabPanel>
            <TabPanel key="followers" class="bg-white p-3 shadow">
              Followings Content
            </TabPanel>
            <TabPanel key="followers" class="bg-white p-3 shadow">
              Photos Content
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>

</style>