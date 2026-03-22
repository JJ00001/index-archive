import type {Company} from "@/interfaces/Company.ts";

export interface IndexActivity {
    id: number
    description: string
    date: string
    company: Company
}