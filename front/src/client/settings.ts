import { http } from '@/http.ts';

export const settingsClient = {
  async get(key: string): string | null | undefined {
    try {
      const result = await http.get(`settings/${key}`);
      if (result.data.found) {
        return result.data.setting.value;
      }
    } catch (e: AxiosError) {
    }
  },
  async set(key: string, value: string): boolean {
    try {
      await http.post('settings', {
        key,
        value,
      });
      return true;
    } catch (e: AxiosError) {
      return false;
    }
  },
};
