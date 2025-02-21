import type { GetManyParamsType } from '@/types/common.ts';

export const clientHelper = {
  makeGetManyParams(inputParams: Partial<GetManyParamsType>) {
    const normalizedParams: GetManyParamsType = {
      page: 1,
      perPage: 10,
      sortField: 'id',
      sortOrder: 'asc',
      ...inputParams,
    };
    const sortSign = normalizedParams.sortOrder === 'asc' ? '' : '-';
    const result = {
      page: normalizedParams.page,
      per_page: normalizedParams.perPage,
      sort: sortSign + normalizedParams.sortField,
    };
    return result;
  },
};
