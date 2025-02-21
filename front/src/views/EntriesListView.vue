<script setup lang="ts">
import { entriesClient } from '@/client/entries.ts';
import type { GetManyParamsType, GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import type { Ref } from 'vue';
import type { DataTablePageEvent } from 'primevue';
import { EntryStatusEnum } from '../enums/EntryStatus.ts';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { TrashIcon } from '@heroicons/vue/24/solid';
import { useEventBus } from '@vueuse/core';

const router = useRouter();
const bus = useEventBus('entries-list');

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
bus.on((event) => {
  if (event === 'refresh') {
    updateTable();
  }
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
const createRecord = () => {
  router.push({ name: 'entry.create' });
};
const editRecord = (id: number) => {
  router.push({ name: 'entry.edit', params: { id } });
};
const deleteRecord = (id: number) => {
  console.log({ id });
};
</script>
<template>
  <div>
    <h1>Entries</h1>
    <div>
      <Button @click="createRecord">Create</Button>
    </div>
    <div v-if="entries">
      <DataTable
          :value="entries.data"
          data-key="id"
          state-key="entries-table"
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
        <Column header="Actions">
          <template #body="{ data }">
            <div class="flex justify-center">
              <Button
                  class="p-1"
                  @click="editRecord(data.id)"
                  rounded
              >
                <PencilSquareIcon class="size-4 text-white" />
              </Button>
              <Button
                  class="p-1 ml-2"
                  @click="deleteRecord(data.id)"
                  rounded
                  severity="danger"
              >
                <TrashIcon class="size-4 text-white" />
              </Button>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>
  </div>
  <router-view />
</template>
