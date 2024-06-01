<template>
  <teleport to="body">
    <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
      <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
            class="flex min-h-full items-center justify-center p-4 text-center"
        >
          <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
          >
            <DialogPanel
                class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
            >
              <DialogTitle
                  as="h3"
                  class="flex items-center justify-between py-2 px-3 text-lg font-medium leading-6 text-gray-900"
              >
                Update Post
                <button @click="closeModal" class="flex items-center justify-center w-8 h-8 rounded-full transition hover:bg-black/5">
                  <XMarkIcon class="w-5 h-5"/>
                </button>
              </DialogTitle>
              <div class="p-3 mt-2">
                <PostUserHeader :post="post" :show-time="false" class="mb-3"/>
                <ckeditor :editor="editor" v-model="form.body" :config="editorConfig"></ckeditor>
<!--                <InputTextarea v-model="form.body" class="mb-3 w-full" />-->
              </div>

              <div class="py-2 px-3">
                <button
                    type="button"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full"
                    @click="submit"
                >
                  Submit
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
  </teleport>
</template>

<script setup>
import {computed, onMounted, reactive, ref, watch} from 'vue'
import {XMarkIcon} from "@heroicons/vue/24/solid";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import PostUserHeader from './PostUserHeader.vue'
import {useForm} from "@inertiajs/vue3";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

const props = defineProps({
  post: {
    type: Object,
    required: true
  },
  modelValue: Boolean
})
const editor = ClassicEditor
const editorConfig = {toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'undo', 'redo', '|', 'blockQuote']}

const form = useForm({
  id: null,
  body: ''
})

const show = computed({
  get: ()=> props.modelValue,
  set: (value)=> emit('update:modelValue', value)
})

const emit = defineEmits(['update:modelValue'])

watch(()=> props.post, ()=>{
  form.id = props.post.id
  form.body = props.post.body
})

function closeModal() {
  show.value = false
  emit('update:modelValue', false)
}

function submit(){
  form.put(route('post.update', {
    post: props.post.id
  }), {
    preserveScroll: true,
    onSuccess: ()=> closeModal()
  })
}
</script>
