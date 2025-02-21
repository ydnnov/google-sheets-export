<script setup lang="ts">
import { useRouter } from 'vue-router';
import { entriesClient } from '@/client/entries.ts';
import { onMounted, type Ref } from 'vue';
import type { EntryType } from '@/types/entry.ts';
import { EntryStatusEnum } from '@/enums/EntryStatus.ts';
import { useToast } from 'primevue';
import { useEventBus } from '@vueuse/core';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const entriesListBus = useEventBus('entries-list');

const mode = computed(() => route.params.id ? 'edit' : 'create');
const modalHeader = computed(() =>
    mode.value === 'create' ? 'Create entry' : 'Edit entry',
);
const gotoList = () => {
  router.push({ name: 'entry.list' });
};
const formErrors = ref({});
const save = async () => {
  if (mode.value === 'create') {
    const result = await entriesClient.create(entry.value);
    if (result.success) {
      formErrors.value = {};
      entry.value = result.data;
      toast.add({
        severity: 'info',
        summary: 'Success',
        detail: 'Entry successfully created',
        life: 3000,
      });
      entriesListBus.emit('refresh');
      router.push({ name: 'entry.edit', params: { id: result.data.id } });
    } else {
      formErrors.value = result.details.errors;
    }
  } else {
    const result = await entriesClient.update(entry.value.id, entry.value);
    if (result.success) {
      formErrors.value = {};
      entry.value = result.data;
      toast.add({
        severity: 'info',
        summary: 'Success',
        detail: 'Entry successfully updated',
        life: 3000,
      });
      entriesListBus.emit('refresh');
      router.push({ name: 'entry.list' });
    } else {
      formErrors.value = result.details.errors;
    }
  }
};
const entry: Ref<EntryType> = ref({});
const loadEntry = async () => {
  entry.value = await entriesClient.getOne(route.params.id);
};
onMounted(() => {
  if (mode.value === 'edit') {
    loadEntry();
  }
});
</script>
<template>
  <Dialog
      v-if="!!entry"
      :visible="true"
      @update:visible="gotoList"
      modal
      @hide="gotoList"
      :header="modalHeader"
      class="w-180"
  >
    <div class="flex flex-col gap-4">
      <div class="flex gap-4">
        <div class="font-bold">Status</div>
        <div
            class="flex items-center gap-2"
            v-for="(label, key) in EntryStatusEnum"
            :key="key"
        >
          <RadioButton
              v-model="entry.status"
              :inputId="`EntryStatus_${key}`"
              :name="`EntryStatus_${key}`"
              :value="key"
          />
          <label :for="`EntryStatus_${key}`">{{ label }}</label>
        </div>
      </div>
      <Message v-if="formErrors.status" severity="error">
        <li v-for="error in formErrors.status">
          {{ error }}
        </li>
      </Message>
      <div class="flex flex-col gap-2">
        <label for="Content" class="font-bold">Content</label>
        <Textarea
            id="Content"
            class="resize-none w-full h-30"
            v-model="entry.content"
        />
      </div>
      <Message v-if="formErrors.content" severity="error">
        <li v-for="error in formErrors.content">
          {{ error }}
        </li>
      </Message>
      <div
          class="flex gap-2"
          v-if="mode === 'edit'"
      >
        <div class="font-bold">Created at:</div>
        <div>{{ entry.created_at }}</div>
      </div>
    </div>
    <div class="flex justify-end gap-2 mt-4">
      <Button
          type="button"
          label="Cancel"
          severity="secondary"
          @click="gotoList"
      />
      <Button
          type="button"
          label="Save"
          @click="save"
      />
    </div>
  </Dialog>
</template>
