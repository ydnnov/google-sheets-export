export type RecordType = {
  id: number
  created_at: string
}

export type GetManyParamsType = {
  page: number
  perPage: number
  sortField: string
  sortOrder: 'asc' | 'desc'
}

export type GetManyResponseType<TRecord> = {
  data: TRecord[]
  links: {
    first: string
    last: string
    next: string
    prev: string
  }
  meta: {
    current_page: number
    from: number
    last_page: number
    links: {
      active: boolean
      label: string
      url: string
    }[]
    path: string
    per_page: number
    to: number
    total: number
  }
}

export type GenericSuccessType<TData> = {
  success: true
  data: TData
}
export type GenericErrorType = {
  success: false
  details: any
}
export type GenericResultType<TData> =
  GenericSuccessType<TData> |
  GenericErrorType
