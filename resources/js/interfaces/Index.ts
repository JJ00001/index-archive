import type {IndexProvider} from "@/interfaces/IndexProvider.ts";

export interface Index {
    id: number
    name: string
    index_provider: IndexProvider
    index_holdings_count?: number | null
    currency: string
}
