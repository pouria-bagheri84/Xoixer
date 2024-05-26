<script setup>
import PostItem from "@/Components/app/PostItem.vue";

const post1 = {
  user: {
    id: 1,
    name: "John Doe",
    avatar: "https://randomuser.me/api/portraits/lego/1.jpg"
  },
  group: null,
  attachments: [
    {
      id: 1,
      name: "test3.png",
      url: "https://picsum.photos/1000",
      mime: "image/png"
    },
    {
      id: 2,
      name: "test2.png",
      url: "https://picsum.photos/1000",
      mime: "image/png"
    },
    {
      id: 3,
      name: "test.doc",
      url: "https://picsum.photos/1000",
      mime: "application/msoffice"
    }

  ],
  body: `
  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis deleniti reiciendis repellat vero? Alias atque autem consectetur cumque dolorum eligendi, fugit in iusto molestias nam nemo perferendis quos, soluta voluptatem?</p>

  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis deleniti reiciendis repellat vero? Alias atque autem consectetur cumque dolorum eligendi, fugit in iusto molestias nam nemo perferendis quos, soluta voluptatem?</p>
  `,
  created_at: "2024-11-10 15:35"
}

const post2 = {
  user: {
    id: 1,
    name: "Elon Musk",
    avatar: "https://randomuser.me/api/portraits/lego/2.jpg"
  },
  group: {
    id: 1,
    name: "Laravel Developers"
  },
  body: `
  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis deleniti reiciendis repellat vero? Alias atque autem consectetur cumque dolorum eligendi, fugit in iusto molestias nam nemo perferendis quos, soluta voluptatem?</p>

  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Debitis deleniti reiciendis repellat vero? Alias atque autem consectetur cumque dolorum eligendi, fugit in iusto molestias nam nemo perferendis quos, soluta voluptatem?</p>
  `,
  created_at: "2024-11-10 15:35"
}
</script>


<template>
  <div class="overflow-y-auto">
    <PostItem :post="post1" />
    <PostItem :post="post2" />
  </div>
</template>


<style scoped>

</style>