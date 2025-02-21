<script setup lang="ts">
import { entriesClient } from '@/client/entries.ts';
import type { GetManyParamsType, GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import type { Ref } from 'vue';
import type { DataTablePageEvent } from 'primevue';
import { EntryStatusEnum } from '../enums/EntryStatus.ts';

const entries: Ref<GetManyResponseType<EntryType>> = ref();
const queryParams: GetManyParamsType = reactive({
  page: 1,
  perPage: 10,
  sortField: 'id',
  sortOrder: 'asc',
});
const updateTable = useDebounceFn(async () => {
  entries.value = await entriesClient.getMany(queryParams);
}, 100);
onMounted(() => {
  updateTable();
});
const onUpdatePage = (ev: DataTablePageEvent) => {
  queryParams.page = ev.page + 1;
  queryParams.perPage = ev.rows;
  updateTable();
};
const onUpdateSortField = (field: string) => {
  queryParams.sortField = field;
  queryParams.page = 1;
  updateTable();
};
const onUpdateSortOrder = (order: number) => {
  queryParams.sortOrder = order > 0 ? 'asc' : 'desc';
  queryParams.page = 1;
  updateTable();
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
          @update:sort-field="onUpdateSortField"
          @update:sort-order="onUpdateSortOrder"
      >
        <Column field="id" header="ID" sortable></Column>
        <Column field="status" header="Status" sortable>
          <template #body="{ data }">
            {{ EntryStatusEnum[data.status] }}
          </template>
        </Column>
        <Column field="content" header="Content"></Column>
        <Column field="created_at" header="Created at" sortable></Column>
      </DataTable>
    </div>
  </div>
</template>
