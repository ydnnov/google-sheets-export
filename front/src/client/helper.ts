import type { GetManyParamsType } from '@/types/common.ts';

export const clientHelper = {
  makeGetManyParams(inputParams: Partial<GetManyParamsType>) {
    const withDefaults: Partial<GetManyParamsType> = {
      page: 1,
      perPage: 10,
      ...inputParams,
    };
    const result = {
      page: withDefaults.page,
      per_page: withDefaults.perPage,
    };
    if (inputParams.sortField) {
      const sortSign = inputParams.sortOrder === 'asc' ? '' : '-';
      result.sort = sortSign + inputParams.sortField;
    }
    return result;
  },
};
