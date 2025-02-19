<script setup lang="ts">
import { entriesClient } from '@/client/entries.ts';
import type { GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import type { Ref } from 'vue';

const entries: Ref<GetManyResponseType<EntryType>> = ref();
const firstEntry: Ref<EntryType> = ref();
onMounted(async () => {
  entries.value = await entriesClient.getMany();
  firstEntry.value = await entriesClient.getOne(10);
});
</script>

<template>
  <main>
    <div v-if="firstEntry">
      <div>id: {{ firstEntry.id }}</div>
      <div>status: {{ firstEntry.status }}</div>
      <div>content: {{ firstEntry.content }}</div>
      <div>created_at: {{ firstEntry.created_at }}</div>
    </div>

    <table v-if="entries">
      <tr
          v-for="entry in entries.data"
          :key="entry.id"
      >
        <td>{{ entry.id }}</td>
        <td>{{ entry.status }}</td>
        <td>{{ entry.content }}</td>
        <td>{{ entry.created_at }}</td>
      </tr>
    </table>
  </main>
</template>
