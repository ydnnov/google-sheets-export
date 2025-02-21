<script setup lang="ts">
import { entriesClient } from '@/client/entries.ts';
import type { GetManyParamsType, GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import { type Ref } from 'vue';
import { type DataTablePageEvent, useConfirm, useToast } from 'primevue';
import { EntryStatusEnum } from '../enums/EntryStatus.ts';
import { settingsClient } from '@/client/settings.ts';
import { PencilSquareIcon } from '@heroicons/vue/24/solid';
import { TrashIcon } from '@heroicons/vue/24/solid';

const router = useRouter();
const bus = useEventBus('entries-list');
const confirm = useConfirm();
const toast = useToast();
const storage = useSessionStorage('entries-table');

const entries: Ref<GetManyResponseType<EntryType>> = ref();
const queryParams: GetManyParamsType = reactive({
  page: 1,
  perPage: 10,
  sortField: 'id',
  sortOrder: 'asc',
});
const showProgressBar = ref(false);
const googleSheetUrl = ref();
if (storage.value) {
  try {
    const tableState = JSON.parse(storage.value);
    queryParams.page = (tableState.first / tableState.rows) + 1;
    queryParams.perPage = tableState.rows;
    queryParams.sortField = tableState.sortField;
    queryParams.sortOrder = tableState.sortOrder > 0 ? 'asc' : 'desc';
  } catch (e) {
  }
}
const updateTable = useDebounceFn(async () => {
  entries.value = await entriesClient.getMany(queryParams);
}, 100);
const loadGoogleSheetUrl = async () => {
  const result = await settingsClient.get('google-sheet-url');
  if (result?.length) {
    googleSheetUrl.value = result;
  } else {
    googleSheetUrl.value = '';
  }
};
const saveGoogleSheetUrl = useThrottleFn(async (value: string) => {
  settingsClient.set('google-sheet-url', value);
}, 250);
watch(() => googleSheetUrl.value, saveGoogleSheetUrl);
onMounted(async () => {
  updateTable();
  loadGoogleSheetUrl();
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
const confirmDelete = (event, id: number | 'all') => {
  const message = id === 'all' ?
      'Do you want to delete ALL records?' :
      'Do you want to delete this record?';
  confirm.require({
    target: event.currentTarget,
    message,
    icon: 'pi pi-info-circle',
    rejectProps: {
      label: 'Cancel',
      severity: 'secondary',
      outlined: true,
    },
    acceptProps: {
      label: 'Delete',
      severity: 'danger',
    },
    accept: async () => {
      if (id === 'all') {
        await entriesClient.deleteAll();
      } else {
        await entriesClient.delete(id);
      }
      toast.add({ severity: 'info', summary: 'Confirmed', detail: 'Record deleted', life: 3000 });
      updateTable();
    },
    reject: () => {
    },
  });
};
const confirmGenerate = (event) => {
  confirm.require({
    target: event.currentTarget,
    message: 'Do you want to generate 1000 records?',
    icon: 'pi pi-info-circle',
    rejectProps: {
      label: 'Cancel',
      severity: 'secondary',
      outlined: true,
    },
    acceptProps: {
      label: 'Generate',
      severity: 'info',
    },
    accept: async () => {
      showProgressBar.value = true;
      entriesClient.generate(1000)
          .then(() => {
            showProgressBar.value = false;
            toast.add({ severity: 'info', summary: 'Finished', detail: 'Generated 1000 records', life: 3000 });
            updateTable();
          });
    },
    reject: () => {
    },
  });
};
</script>
<template>
  <ProgressBar
      class="fixed top-0 left-0 right-0 z-100"
      style="height: 10px;"
      v-if="showProgressBar"
      mode="indeterminate"
  />
  <div>
    <h1>Entries</h1>
    <div class="flex">
      <div class="grow">
        <InputText type="text" v-model="googleSheetUrl" />
      </div>
      <div class="flex">
        <Button @click="createRecord">Create</Button>
        <Button
            class="ml-4"
            @click="confirmDelete($event, 'all')"
            severity="danger"
        >Delete all
        </Button>
        <Button
            class="ml-4"
            @click="confirmGenerate($event)"
            severity="info"
        >Generate
        </Button>
      </div>
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
                  @click="confirmDelete($event, data.id)"
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
  <ConfirmPopup></ConfirmPopup>
  <router-view />
</template>
