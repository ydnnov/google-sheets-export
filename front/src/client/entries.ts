import { http } from '@/http.ts';
import type { GetManyParamsType, GetManyResponseType } from '@/types/common.ts';
import type { EntryType } from '@/types/entry.ts';
import { clientHelper } from '@/client/helper.ts';

export const entriesClient = {
  async getMany(
    params: Partial<GetManyParamsType>,
  ): GetManyResponseType<EntryType> {
    const response = await http.get('entries', {
      params: clientHelper.makeGetManyParams(params),
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
