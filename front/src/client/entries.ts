import { http } from '@/http.ts';
import type { GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';

export const entriesClient = {
  async getMany(): GetManyResponseType<EntryType> {
    const response = await http.get('entries');
    return response.data;
  },
  async getOne(id: number): EntryType | null {
    try {
      const response = await http.get(`entries/${id}`);
      return response.data.data;
    } catch (AxiosError) {
      return null;
    }
  },
};
