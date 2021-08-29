<template>
  <div class="w-screen h-screen grid grid-cols-6">
    <div class="col-span-1 overflow-x-hidden overflow-y-auto flex flex-col">
      <router-link
        v-for="tran in cnTrans"
        :key="tran"
        class="py-2 px-4 w-full text-left text-gray-600 border-b"
        :to="`/translations/${tran}`"
        :title="tran"
      >
        {{ tran }}
      </router-link>
    </div>
    <div class="col-span-5 overflow-y-auto">
      <router-view></router-view>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "@vue/runtime-core";
import { ref } from "vue";
import { HttpClient } from "./libs/http";
const client = new HttpClient({});

const cnTrans = ref<string[]>([]);

const fetchSource = async () => {
  cnTrans.value = await client.get<string[]>("api/sources/china/translations");
};
onMounted(() => {
  fetchSource();
});
</script>

<style scoped>
.router-link-exact-active {
  background-color: rgba(75, 85, 99);
  color: white;
  user-select: none;
}
</style>
