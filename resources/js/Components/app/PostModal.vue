<script setup>
import {computed, ref, watch} from 'vue'
import {XMarkIcon, PaperClipIcon, BookmarkIcon, ArrowUturnLeftIcon} from "@heroicons/vue/24/solid";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import PostUserHeader from './PostUserHeader.vue'
import {useForm, usePage} from "@inertiajs/vue3";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic"
import {isImage} from "@/helpers.js";

const props = defineProps({
  post: {
    type: Object,
    required: true
  },
  modelValue: Boolean
})
const editor = ClassicEditor
const editorConfig = {
  toolbar: ['heading', '|', 'bold', 'italic', '|', 'link', '|', 'bulletedList', 'numberedList', '|', 'outdent', 'indent', '|', 'undo', 'redo', '|', 'blockQuote'],
}
const attachmentFiles = ref([])
const attachmentErrors = ref([])
const showExtensionsText = ref(false)
const emit = defineEmits(['update:modelValue', 'hide'])

const attachmentExtensions = usePage().props.attachmentExtensions;

const form = useForm({
  id: null,
  body: '',
  attachments: [],
  deleted_file_ids: [],
  _method: 'POST'
})

const show = computed({
  get: ()=> props.modelValue,
  set: (value)=> emit('update:modelValue', value)
})

const computedAttachments = computed(()=>{
  return [...attachmentFiles.value, ...(props.post.attachments || [])]
})


watch(()=> props.post, ()=>{
  form.body = props.post.body || ''
})

function closeModal() {
  show.value = false
  emit('hide')
  resetModal()
}

function resetModal() {
  form.reset()
  attachmentFiles.value = [];
  attachmentErrors.value = [];
  showExtensionsText.value = false;
  if (props.post.attachments){
    props.post.attachments.forEach(file => file.delete = false)
  }
}

function submit(){
  form.attachments = attachmentFiles.value.map(myfile => myfile.file)
  if (props.post.id){
    form._method = 'PUT'
    form.post(route('post.update', props.post.id), {
      preserveScroll: true,
      onSuccess: ()=> {
        closeModal()
      },
      onError: (err)=>{
        processErrors(err)
      }
    })
  }else {
    form.post(route('post.store'), {
      preserveScroll: true,
      onSuccess: ()=> {
        closeModal()
      },
      onError: (err)=>{
        processErrors(err)
      }
    })
  }
}

async function onAttachmentChoose($event) {
  showExtensionsText.value = false
  for (const file of $event.target.files) {
    let part = file.name.split('.')
    let ext = part.pop().toLowerCase();
    if (!attachmentExtensions.includes(ext)){
      showExtensionsText.value = true
    }
    const myFile = {
      file,
      url: await readFile(file)
    }
    attachmentFiles.value.push(myFile)
  }
  $event.target.value = null;
}

async function readFile(file) {
  return new Promise((res, rej)=>{
    if (isImage(file)) {
      const reader = new FileReader()
      reader.onload = ()=>{
        res(reader.result)
      }
      reader.onerror = rej
      reader.readAsDataURL(file)
    }else {
      res(null)
    }
  })
}

function removeFile(file) {
  if (file.file){
    attachmentFiles.value = attachmentFiles.value.filter(f => f !== file)
  }else{
    form.deleted_file_ids.push(file.id)
    file.delete = true
  }
}

function undoDeleted(file) {
  file.delete = false
  form.deleted_file_ids = form.deleted_file_ids.filter(id => file.id !== id)

}

function processErrors(err) {
  for (const key in err) {
    if (key.includes('.')){
      const [, index] = key.split('.')
      attachmentErrors.value[index] = err[key]
    }
  }
}
</script>


<template>
  <teleport to="body">
    <TransitionRoot appear :show="show" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-50">
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
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all">
              <DialogTitle as="h3" class="flex items-center justify-between py-2 px-3 text-lg font-medium leading-6 text-gray-900">
                {{ post.id ? 'Update Post' : 'Create Post' }}
                <button @click="closeModal" class="flex items-center justify-center w-8 h-8 rounded-full transition hover:bg-black/5">
                  <XMarkIcon class="w-5 h-5"/>
                </button>
              </DialogTitle>
              <div class="p-3 mt-2">
                <PostUserHeader :post="post" :show-time="false" class="mb-3"/>
                <ckeditor :editor="editor" v-model="form.body" :config="editorConfig"></ckeditor>
                <div v-if="showExtensionsText" class="border-l-4 border-amber-500 bg-amber-100 py-2 px-3 mt-3 text-gray-800">
                  Files Must Be One Of The Following Extensions:
                  <br>
                  <small>{{attachmentExtensions.join(', ')}}</small>
                </div>
                <div class="grid gap-3 my-4" :class="[computedAttachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2']">
                  <template v-for="(myFile, index) of computedAttachments">
                    <div class="group aspect-square bg-indigo-50 text-gray-500 flex flex-col items-center justify-center relative border-2" :class="attachmentErrors[index]? 'border-red-600' : '' ">
                      <div v-if="myFile.delete" class="absolute z-10 left-0 bottom-0 right-0 py-2 px-3 text-sm bg-black text-white flex justify-between items-center">
                        Deleted
                        <ArrowUturnLeftIcon @click="undoDeleted(myFile)" class="w-4 h-4 cursor-pointer"/>
                      </div>
                      <button @click="removeFile(myFile)" class="absolute right-1 top-1 z-20 p-1 hover:border hover:border-1 hover:border-gray-100 bg-gray-800 hover:text-white text-gray-500 text-xs flex items-center rounded-full">
                        <XMarkIcon class="h-5 w-5"/>
                      </button>
                      <img v-if="isImage(myFile.file || myFile)" :src="myFile.url" alt="" class="object-contain aspect-square" :class="myFile.delete ? 'opacity-50' : ''">
                      <div v-else class="flex flex-col justify-center items-center px-3" :class="myFile.delete ? 'opacity-50' : ''">
                        <PaperClipIcon class="w-11 h-11 mb-3"/>
                        <small class="text-center">{{ (myFile.file || myFile).name }}</small>
                      </div>
                      <small v-if="attachmentErrors[index]" class="text-red-500 text-sm text-center p-1 absolute left-0 right-0 bottom-0">{{attachmentErrors[index]}}</small>
                    </div>
                  </template>
                </div>
              </div>

              <div class="flex gap-2 py-2 px-3">
                <button
                    type="button"
                    class="flex items-center justify-center relative rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full"
                    @click="submit"
                >
                  <PaperClipIcon class="w-4 h-4 mr-1"/>
                  Attach Files
                  <input @click.stop @change="onAttachmentChoose" type="file" multiple class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
                </button>

                <button
                    type="button"
                    class="flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 w-full"
                    @click="submit"
                >
                  <BookmarkIcon class="w-4 h-4 mr-1"/>
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