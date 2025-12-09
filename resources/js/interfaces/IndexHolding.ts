import type {Company} from "@/interfaces/Company.ts";
import type {Index} from "@/interfaces/Index.ts";

export interface IndexHolding {
    id: number
    index_holding_id: number // TODO straighten out to use id
    index_id: number
    company_id: number
    is_active: boolean
    company?: Company
    index?: Index
}