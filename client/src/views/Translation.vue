<template>
  <div>
    <table class="w-full">
      <thead>
        <tr>
          <th class="bg-red-600 text-white font-medium py-2">Tiếng Trung</th>
          <th class="bg-green-400 text-white font-medium py-2">
            Tiếng Việt tham khảo
          </th>
          <th class="bg-yellow-400 text-white font-medium py-2">Bản dịch</th>
        </tr>
      </thead>
      <tbody>
        <slot v-for="(value, key) in cnTranslations" :key="key">
          <tr>
            <td colspan="3" class="px-3 pt-2">
              <h4 class="text-gray-800 text-sm">{{ key }}</h4>
            </td>
          </tr>
          <tr>
            <td class="border-b px-3 pb-2">
              <p class="font-medium">
                {{ value }}
              </p>
            </td>
            <td class="border-b px-3 py-2">
              <button
                class="font-medium"
                @click="() => setTranslation(key, refTranslations[key])"
              >
                {{ refTranslations[key] }}
              </button>
            </td>
            <td class="border-b min-w-80">
              <textarea class="border w-full" />
            </td>
          </tr>
        </slot>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { onMounted, ref, watch } from "vue";
import { client } from "../libs/api";

const route = useRoute();
const cnTranslations = ref();
const refTranslations = ref();

const fetchTranslations = async (file: string) => {
  cnTranslations.value = await client.get(
    `/api/sources/china/translations/${file}`
  );
  refTranslations.value = await client.get(
    `/api/sources/ref/translations/${file}`
  );
};

const setTranslation = (key: string, value: string) => {
  console.log(key, value);
};

onMounted(() => {
  fetchTranslations(route.params.file.toString());
});
watch(
  () => route.params.file,
  async (newId) => {
    fetchTranslations(newId.toString());
  }
);
</script>
