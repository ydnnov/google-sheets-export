<script setup lang="ts">
import { entriesClient } from '@/client/entries.ts';
import type { GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import type { Ref } from 'vue';
import type { DataTablePageEvent } from 'primevue';

const entries: Ref<GetManyResponseType<EntryType>> = ref();
onMounted(async () => {
  entries.value = await entriesClient.getMany();
});
const onUpdatePage = async (ev: DataTablePageEvent) => {
  entries.value = await entriesClient.getMany({
    page: ev.page + 1,
    per_page: ev.rows,
  });
};
</script>
<template>
  <div class="about">
    <h1>Entries</h1>
    <div v-if="entries">
      <DataTable
          :value="entries.data"
          data-key="id"
          lazy
          paginator
          paginator-template="PageLinks FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink RowsPerPageDropdown"
          current-page-report-template="{first} to {last} of {totalRecords}"
          :page-link-size="7"
          paginator-position="both"
          :rows-per-page-options="[5, 10, 20, 50]"
          :rows="5"
          :total-records="entries.meta.total"
          @page="onUpdatePage"
      >
        <Column field="id" header="ID"></Column>
        <Column field="status" header="Status"></Column>
        <Column field="content" header="Content"></Column>
        <Column field="created_at" header="Created at"></Column>
      </DataTable>
    </div>
  </div>
</template>
