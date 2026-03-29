export interface Company {
    id: number,
    name: string,
    ticker: string,
    logo?: string | null,
    isin: string,
    sector_id: number,
    country_id: number,
    exchange_id: number,
    currency_id: number,
    asset_class_id: number,
}
