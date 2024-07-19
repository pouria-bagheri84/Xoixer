<script setup>
import {computed, ref} from 'vue'
import {XMarkIcon, CheckCircleIcon, CameraIcon} from '@heroicons/vue/24/solid'
import {TabGroup, TabList, Tab, TabPanels, TabPanel} from '@headlessui/vue'
import {useForm, Head, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TabItem from "@/Pages/Profile/Partials/TabItem.vue";
import PrimaryButton from "../../Components/PrimaryButton.vue"
import InviteUserModal from "./InviteUserModal.vue"
import UsersListItem from "@/Components/app/UsersListItem.vue";
import TextInput from "../../Components/TextInput.vue"
import GroupForm from "@/Components/app/GroupForm.vue";
import PostList from "@/Components/app/PostList.vue";
import CreatePost from "@/Components/app/CreatePost.vue";


const imagesForm = useForm({
  thumbnail: null,
  cover: null,
})

const aboutForm = useForm({
  name: usePage().props.group.name,
  auto_approval: !!parseInt(usePage().props.group.auto_approval),
  about: usePage().props.group.about
})

let showNotification = ref(true);


const props = defineProps({
  success: {
    type: String,
  },
  group: {
    type: Object,
  },
  posts: Object,
  errors: String,
  users: Array,
  requests: Array
});

const isCurrentUserAdmin = computed(() => props.group.role === "admin")
const isJoinedToGroup = computed(() => !!props.group.role && props.group.status === "approved")

const coverImageSrc = ref('')
const thumbnailImageSrc = ref('')
const showInviteUserModal = ref(false)
const searchKeywords = ref('')
const authUser = usePage().props.auth.user;

function onCoverChange(event) {
  imagesForm.cover = event.target.files[0]
  if (imagesForm.cover) {
    const reader = new FileReader()
    reader.onload = () => {
      coverImageSrc.value = reader.result
    }
    reader.readAsDataURL(imagesForm.cover)
  }
}

function onThumbnailChange(event) {
  imagesForm.thumbnail = event.target.files[0]
  if (imagesForm.thumbnail) {
    const reader = new FileReader()
    reader.onload = () => {
      thumbnailImageSrc.value = reader.result
    }
    reader.readAsDataURL(imagesForm.thumbnail)
  }
}

function cancelCoverImage() {
  imagesForm.cover = null
  coverImageSrc.value = null
}

function cancelThumbnailImage() {
  imagesForm.thumbnail = null
  thumbnailImageSrc.value = null
}

function submitCoverImage() {
  imagesForm.post(route('group.update.images', props.group.slug), {
    preserveScroll: true,
    onSuccess: () => {
      showNotification.value = true
      cancelCoverImage()
      setTimeout(() => {
        showNotification.value = false
      }, 3000)
    }
  })
}

function submitThumbnailImage() {
  imagesForm.post(route('group.update.images', props.group.slug), {
    preserveScroll: true,
    onSuccess: () => {
      showNotification.value = true
      cancelThumbnailImage()
      setTimeout(() => {
        showNotification.value = false
      }, 3000)
    }
  })
}

function joinToGroup() {
  useForm({}).post(route('group.join.users', props.group.slug), {
    preserveScroll: true
  })
}

function approveUser(user) {
  useForm({
    user_id: user.id,
    action: 'approve'
  }).post(route('group.approve.requests', props.group.slug)), {
    preserveScroll: true
  }
}

function rejectUser(user) {
  useForm({
    user_id: user.id,
    action: 'reject'
  }).post(route('group.approve.requests', props.group.slug), {
    preserveScroll: true
  })
}

function onRoleChange(user, role) {
  useForm({
    user_id: user.id,
    role: role
  }).post(route('group.change.role', props.group.slug), {
    preserveScroll: true
  })
}

function updateGroup() {
  aboutForm.put(route('group.update', props.group.slug), {
    preserveScroll: true
  })
}

function deleteUser(user) {
  if (!window.confirm(`Are You Sure You Want Remove User "${user.name}" From This Group?`)) {
    return false;
  }

  useForm({
    user_id: user.id,
  }).delete(route('group.remove.user', props.group.slug), {
    preserveScroll: true
  })
}
</script>

<template>
  <Head :title="group.name"/>
  <AuthenticatedLayout>
    <div class="max-w-[850px] mx-auto h-full overflow-auto">
      <div v-show="showNotification && success"
           class="my-2 py-2 px-3 rounded font-medium text-sm bg-emerald-500 text-white">
        {{ success }}
      </div>
      <div v-if="errors.cover" class="my-2 py-2 px-3 rounded font-medium text-sm bg-red-500 text-white">
        {{ errors.cover }}
      </div>
      <div class="p-4">
        <div class="group relative bg-white">
          <img class="w-full h-[200px] object-cover" :src="coverImageSrc || group.cover_url || '/img/cover.jpg'" alt="">
          <div v-if="isCurrentUserAdmin" class="absolute top-2 right-2">
            <button v-if="!coverImageSrc"
                    class="py-1 px-2 bg-gray-50 hover:bg-gray-100 text-gray-500 text-xs flex items-center rounded opacity-0 transition-all group-hover:opacity-100">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                   stroke="currentColor" class="size-3 mr-1">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
              </svg>
              Update Cover Image
              <input type="file" class="absolute top-0 left-0 bottom-0 right-0 opacity-0 cursor-pointer"
                     @change="onCoverChange">
            </button>
            <div v-else class="flex gap-3 bg-white p-2 opacity-0 transition-all group-hover:opacity-100 rounded">
              <button @click="cancelCoverImage"
                      class="py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-500 text-xs flex items-center rounded">
                <XMarkIcon class="h-3 w-3 mr-1"/>
                Cancel
              </button>
              <button @click="submitCoverImage"
                      class="py-1 px-2 bg-gray-700 hover:bg-gray-900 text-gray-100 text-xs flex items-center rounded">
                <CheckCircleIcon class="h-3 w-3 mr-1"/>
                Save
              </button>
            </div>
          </div>
          <div class="flex">
            <div class="flex justify-center rounded-full items-center h-[128px] w-[128px] -mt-[64px] ml-[48px] relative group/thumbnail">
              <img class="w-full h-full object-cover rounded-full"
                   :src="thumbnailImageSrc || group.thumbnail_url || 'https://sm.ign.com/ign_nordic/cover/a/thumbnail-gen/thumbnail-generations_prsz.jpg'"
                   alt="">
              <div v-if="isCurrentUserAdmin"
                   class="absolute left-0 bottom-0 top-0 right-0 group-hover/thumbnail:bg-gray-200/50 rounded-full">
                <button v-if="!thumbnailImageSrc"
                        class="absolute left-0 top-0 right-0 bottom-0 bg-black/25 rounded-full flex justify-center items-center opacity-0 group-hover/thumbnail:opacity-100">
                  <CameraIcon class="h-16 w-16"/>
                  <input type="file" class="absolute top-0 left-0 bottom-0 right-0 opacity-0 cursor-pointer"
                         @change="onThumbnailChange">
                </button>
                <div v-else
                     class="absolute top-10 right-3 flex gap-3 bg-white p-2 opacity-0 transition-all group-hover/thumbnail:opacity-100 rounded">
                  <button @click="cancelThumbnailImage"
                          class="py-1 px-2 bg-gray-200 hover:bg-gray-300 text-gray-500 text-xs flex items-center rounded">
                    <XMarkIcon class="h-5 w-5"/>
                  </button>
                  <button @click="submitThumbnailImage"
                          class="py-1 px-2 bg-gray-700 hover:bg-gray-900 text-gray-100 text-xs flex items-center rounded">
                    <CheckCircleIcon class="h-5 w-5"/>
                  </button>
                </div>
              </div>
            </div>
            <div class="flex justify-between items-center flex-1 p-4">
              <h2 class="font-bold text-lg">{{ group.name }}</h2>

              <PrimaryButton v-if="!authUser"
                             :href="route('login')">
                Login to join to this group
              </PrimaryButton>

              <PrimaryButton v-if="isCurrentUserAdmin"
                             @click="showInviteUserModal = true">
                Invite Users
              </PrimaryButton>
              <PrimaryButton v-if="authUser && !group.role && group.auto_approval"
                             @click="joinToGroup">
                Join to Group
              </PrimaryButton>
              <PrimaryButton v-if="authUser && !group.role && !group.auto_approval"
                             @click="joinToGroup">
                Request to join
              </PrimaryButton>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t p-4 pt-0">
        <TabGroup>
          <TabList class="flex bg-white">
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Posts" :selected="selected"/>
            </Tab>
            <Tab v-if="isJoinedToGroup" v-slot="{ selected }" as="template">
              <TabItem text="Users" :selected="selected"/>
            </Tab>
            <Tab v-if="isCurrentUserAdmin" v-slot="{ selected }" as="template">
              <TabItem text="Pending Requests" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="Photos" :selected="selected"/>
            </Tab>
            <Tab v-slot="{ selected }" as="template">
              <TabItem text="About" :selected="selected"/>
            </Tab>
          </TabList>

          <TabPanels class="mt-2">
            <TabPanel>
              <template v-if="posts">
                <CreatePost :group="group"/>
                <PostList :posts="posts.data" class="flex-1"/>
              </template>
              <div v-else class="py-8 text-center">
                You don't have permission to view these posts.
              </div>
            </TabPanel>
            <TabPanel v-if="isJoinedToGroup">
              <TextInput :model-value="searchKeywords" placeholder="Type to search..." class="w-full mb-4"/>
              <div class="grid grid-cols-2 gap-3">
                <UsersListItem
                    v-for="user of users"
                    :user="user"
                    :key="user.id"
                    :show-role-dropdown="isCurrentUserAdmin"
                    :disable-role-dropdown="group.user_id === user.id"
                    @role-change="onRoleChange"
                    @delete="deleteUser"
                    class="rounded-lg shadow"/>
              </div>
            </TabPanel>
            <TabPanel v-if="isCurrentUserAdmin">
              <div v-if="requests.length" class="grid grid-cols-2 gap-3">
                <UsersListItem
                    v-for="user of requests"
                    :user="user"
                    :for-approve="true"
                    :key="user.id"
                    class="rounded-lg shadow"
                    @approve="approveUser"
                    @reject="rejectUser"/>
              </div>
              <div v-else class="py-8 text-center">
                There Are Not Pending Requests
              </div>
            </TabPanel>
            <TabPanel class="bg-white p-3 shadow">
              Photos Content
            </TabPanel>
            <TabPanel class="bg-white p-3 shadow">
              <template v-if="isCurrentUserAdmin">
                <GroupForm :form="aboutForm"/>
                <PrimaryButton @click="updateGroup">
                  submit
                </PrimaryButton>
              </template>
              <div v-else v-html="group.about"></div>
            </TabPanel>
          </TabPanels>
        </TabGroup>
      </div>
    </div>
  </AuthenticatedLayout>
  <InviteUserModal v-model="showInviteUserModal"/>
</template>

<style>

</style>