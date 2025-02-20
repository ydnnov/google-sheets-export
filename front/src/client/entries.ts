import { http } from '@/http.ts';
import type { GetManyParamsType, GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';

export const entriesClient = {
  async getMany(
    params: Partial<GetManyParamsType>,
  ): GetManyResponseType<EntryType> {
    const usedParams: GetManyParamsType = {
      page: 1,
      per_page: 10,
      ...params,
    };
    const response = await http.get('entries', {
      params: usedParams,
    });
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
