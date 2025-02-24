import { http } from '@/http.ts';
import type {
  GenericResultType,
  GetManyParamsType,
  GetManyResponseType,
} from '@/types/common.ts';
import type { EntryBaseType, EntryType } from '@/types/entry.ts';
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
  async create(
    formData: EntryBaseType,
  ): GenericResultType<EntryType> {
    try {
      const response = await http.post('entries', formData);
      return {
        success: true,
        data: response.data.data,
      };
    } catch (e: AxiosError) {
      return {
        success: false,
        details: e.response.data,
      };
    }
  },
  async update(
    id: number,
    formData: Partial<EntryBaseType>,
  ): GenericResultType<EntryType> {
    try {
      const response = await http.patch(`entries/${id}`, formData);
      return {
        success: true,
        data: response.data.data,
      };
    } catch (e: AxiosError) {
      return {
        success: false,
        details: e.response.data,
      };
    }
  },
  async generate(numRecords: number): boolean {
    if (numRecords > 1000) {
      throw new Error('Maximum of 1000 records is allowed');
    }
    const result = await http.post(`entries/generate`, { numRecords });
    return result.data;
  },
  async delete(id: number): boolean {
    try {
      await http.delete(`entries/${id}`);
      return true;
    } catch (AxiosError) {
      return false;
    }
  },
  async deleteAll(): boolean {
    try {
      await http.post(`entries/delete-all`);
      return true;
    } catch (AxiosError) {
      return false;
    }
  },
};
