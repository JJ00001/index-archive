<?php

namespace App\Http\Services\HoldingImport\DTOs;

/**
 * Market data point for a company at a specific date.
 *
 * Note: companyIsin is a temporary reference used during import.
 * The MarketData table stores index_holding_id (FK), not company_isin.
 * MarketDataService resolves ISIN → IndexHolding ID before database insertion.
 */
class MarketDataPoint
{
    public function __construct(
        public readonly string $companyIsin,
        public readonly float $marketCapitalization,
        public readonly float $weight,
        public readonly float $sharePrice,
        public readonly string $date,
    ) {}

    public function toArray(): array
    {
        return [
            'company_isin' => $this->companyIsin,
            'market_capitalization' => $this->marketCapitalization,
            'weight' => $this->weight,
            'share_price' => $this->sharePrice,
            'date' => $this->date,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
