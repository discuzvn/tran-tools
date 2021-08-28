<template>
  <div class="relative">
    <table class="w-full mb-12">
      <thead>
        <tr>
          <th class="bg-red-600 text-white font-medium py-2 sticky top-0">
            Tiếng Trung
          </th>
          <th class="bg-green-400 text-white font-medium py-2 sticky top-0">
            Tiếng Việt tham khảo
          </th>
          <th class="bg-yellow-400 text-white font-medium py-2 sticky top-0">
            Bản dịch
          </th>
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
              <div class="mt-2">
                <a
                  :href="`https://translate.google.com/?sl=zh-CN&tl=en&text=${value}&op=translate`"
                  target="_blank"
                  class="text-sm text-blue-600"
                  >Google Translate</a
                >
                ·
                <a
                  :href="`https://fanyi.baidu.com/#zh/en/${value}`"
                  target="_blank"
                  class="text-sm text-blue-600"
                  >Baidu</a
                >
              </div>
            </td>
            <td class="border-b px-3 py-2">
              <button
                class="font-medium w-full text-left"
                @click="() => setTranslation(key, refTranslations[key])"
              >
                {{ refTranslations[key] }}
              </button>
            </td>
            <td class="border-b min-w-96 pb-2">
              <textarea
                :class="[
                  'border w-full p-2 rounded-md tran-textarea',
                  statusMap[key],
                ]"
                v-model="toTranslations[key]"
              ></textarea>
            </td>
          </tr>
        </slot>
      </tbody>
    </table>
    <button
      class="fixed bottom-1 right-8 text-white bg-blue-600 px-4 py-2 rounded-md"
      @click="save"
    >
      Lưu bản dịch
    </button>
  </div>
</template>

<script setup lang="ts">
import { useRoute } from "vue-router";
import { onMounted, ref, watch } from "vue";
import { client } from "../libs/api";

const route = useRoute();
const cnTranslations = ref();
const refTranslations = ref();
const edTranslations = ref();
const toTranslations = ref<Record<string, string>>({});
const statusMap = ref<Record<string, string>>({});

const fetchTranslations = async (file: string) => {
  const cnRes = await client.getTry<Record<string, string>>(
    `/api/sources/china/translations/${file}`,
    {}
  );
  const refRes = await client.getTry<Record<string, string>>(
    `/api/sources/ref/translations/${file}`,
    {}
  );

  const edRes = await client.getTry<Record<string, string>>(
    `/api/sources/translated/translations/${file}`,
    {}
  );

  cnTranslations.value = cnRes;
  refTranslations.value = refRes;
  edTranslations.value = edRes;
  toTranslations.value = Object.keys(cnRes).reduce((prev, curr) => {
    return {
      ...prev,
      [curr]: edRes[curr] ?? refRes[curr] ?? cnRes[curr],
    };
  }, {});

  statusMap.value = Object.keys(cnRes).reduce((prev, curr) => {
    return {
      ...prev,
      [curr]: edRes[curr] ? "translated" : refRes[curr] ? "has_reference" : "",
    };
  }, {});
};

const setTranslation = (key: string | number | symbol, value: string) => {
  console.log(key, value);
};

const save = async () => {
  client.post(`/api/translations/${route.params.file}`, toTranslations.value);
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

<style scoped>
.tran-textarea.translated {
  border-color: rgb(52, 211, 153);
}
.tran-textarea.has_reference {
  border-color: rgb(251, 191, 36);
}
</style>
