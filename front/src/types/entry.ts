import type { RecordType } from '@/types/common.ts';

export type EntryStatusType = 'Allowed' | 'Prohibited'

export type EntryBaseType = {
  status: EntryStatusType
  content: string
}

export type EntryType = RecordType & EntryBaseType
