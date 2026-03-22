import type {Company} from "@/interfaces/Company.ts";

export type IndexActivityDescription = 'company_added_to_index' | 'company_removed_from_index'

export interface IndexActivity {
    id: number
    description: IndexActivityDescription
    date: string
    company: Company
}
