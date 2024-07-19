<script setup>
import {computed, ref} from 'vue'
import {XMarkIcon, CheckCircleIcon, CameraIcon} from '@heroicons/vue/24/solid'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import {useForm, usePage, Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import Edit from "@/Pages/Profile/Edit.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";

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
  success: {
    type: String,
  },
  user: {
    type: Object,
  },
  isCurrentUserFollower: Boolean,
  followerCount: Number,
  errors: String
});

const isMyProfile = computed(()=> {
  return authUser && authUser.id === props.user.id
})

const coverImageSrc = ref('')
const avatarImageSrc = ref('')

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
function onAvatarChange(event){
  imagesForm.avatar = event.target.files[0]
  if (imagesForm.avatar){
    const reader = new FileReader()
    reader.onload = ()=>{
      avatarImageSrc.value = reader.result
    }
    reader.readAsDataURL(imagesForm.avatar)
  }
}

function cancelCoverImage(){
  imagesForm.cover = null
  coverImageSrc.value = null
}

function cancelAvatarImage(){
  imagesForm.avatar = null
  avatarImageSrc.value = null
}

function submitCoverImage(){
  imagesForm.post('/profile/update-images', {
    preserveScroll: true,
    onSuccess: (user)=> {
      showNotification.value = true
      cancelCoverImage()
      setTimeout(()=>{
        showNotification.value = false
      }, 3000)
    }
  })
}

function submitAvatarImage(){
  imagesForm.post('/profile/update-images', {
    preserveScroll: true,
    onSuccess: (user)=> {
      showNotification.value = true
      cancelAvatarImage()
      setTimeout(()=>{
        showNotification.value = false
      }, 3000)
    }
  })
}

function followUser() {
  useForm({
    follow: !props.isCurrentUserFollower
  }).post(route('user.follow', props.user.id), {
    preserveScroll: true
  })
}
</script>

<template>
  <Head :title="user.name"/>
  <AuthenticatedLayout>
    <div class="max-w-[850px] mx-auto h-full overflow-auto">
      <div v-show="showNotification && success" class="my-2 py-2 px-3 rounded font-medium text-sm bg-emerald-500 text-white">
        {{ success }}
      </div>
      <div v-if="errors.cover" class="my-2 py-2 px-3 rounded font-medium text-sm bg-red-500 text-white">
        {{ errors.cover }}
      </div>
      <div class="group relative bg-white">
        <img class="w-full h-[200px] object-cover" :src="coverImageSrc || user.cover_url || '/img/cover.jpg'" alt="">
        <div class="absolute top-2 right-2" v-if="isMyProfile">
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
          <div class="flex justify-center rounded-full items-center h-[128px] w-[128px] -mt-[64px] ml-[48px] relative group/avatar">
            <img class="w-full h-full object-cover rounded-full" :src="avatarImageSrc || user.avatar_url" alt="">
            <div v-if="isMyProfile" class="absolute left-0 bottom-0 top-0 right-0 group-hover/avatar:bg-gray-200/50 rounded-full">
              <button v-if="!avatarImageSrc" class="absolute left-0 top-0 right-0 bottom-0 bg-black/25 rounded-full flex justify-center items-center opacity-0 group-hover/avatar:opacity-100">
                <CameraIcon class="h-16 w-16"/>
                <input type="file" class="absolute top-0 left-0 bottom-0 right-0 opacity-0 cursor-pointer" @change="onAvatarChange">
              </button>
              <div v-else class="absolute top-10 right-3 flex gap-3 bg-white p-2 opacity-0 transition-all group-hover/avatar:opacity-100 rounded">
                <button @click="cancelAvatarImage" class="py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-500 text-xs flex items-center rounded">
                  <XMarkIcon class="h-5 w-5"/>
                </button>
                <button @click="submitAvatarImage" class="py-1 px-2 bg-gray-700 hover:bg-gray-900 text-gray-100 text-xs flex items-center rounded">
                  <CheckCircleIcon class="h-5 w-5"/>
                </button>
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center flex-1 p-4">
            <div>
              <h2 class="font-bold text-lg">{{ user.name }}</h2>
              <p class="text-xs text-gray-500">{{followerCount}} follower(s)</p>
            </div>

            <div>
              <PrimaryButton v-if="!isCurrentUserFollower" @click="followUser">
                Follow User
              </PrimaryButton>
              <DangerButton v-else @click="followUser">
                Unfollow User
              </DangerButton>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t">
        <TabGroup>
          <TabList class="flex bg-white">
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
            <Tab v-if="isMyProfile" v-slot="{ selected }" as="template">
              <TabItem text="My Profile" :selected="selected"/>
            </Tab>
          </TabList>

          <TabPanels class="mt-2">
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
            <TabPanel v-if="isMyProfile" key="posts">
              <Edit :must-verify-email="mustVerifyEmail"/>
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>

</style>