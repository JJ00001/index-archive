<?php

namespace App\Http\Services\HoldingImport\DTOs;

class CompanyData
{
    public function __construct(
        public readonly string $isin,
        public readonly string $ticker,
        public readonly string $name,
        public readonly int $sectorId,
        public readonly int $countryId,
        public readonly int $exchangeId,
        public readonly int $currencyId,
        public readonly int $assetClassId,
    ) {}

    /**
     * Convert to array for database upsert operation
     * Matches Company model's fillable fields
     */
    public function toArray(): array
    {
        return [
            'isin' => $this->isin,
            'ticker' => $this->ticker,
            'name' => $this->name,
            'sector_id' => $this->sectorId,
            'country_id' => $this->countryId,
            'exchange_id' => $this->exchangeId,
            'currency_id' => $this->currencyId,
            'asset_class_id' => $this->assetClassId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
