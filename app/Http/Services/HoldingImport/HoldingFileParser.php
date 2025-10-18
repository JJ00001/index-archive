<?php

namespace App\Http\Services\HoldingImport;

use App\Http\Services\HoldingImport\DTOs\CompanyData;
use App\Http\Services\HoldingImport\DTOs\MarketDataPoint;
use App\Models\AssetClass;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Index;
use App\Models\Sector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use JsonException;

class HoldingFileParser
{
    public function __construct(private EntityResolver $entityResolver) {}

    /**
     * @throws JsonException
     */
    public function parse(Index $index, string $fullFilePath): array
    {
        $fileContent = file_get_contents($fullFilePath);
        $jsonData = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        $dateFromFilename = pathinfo(basename($fullFilePath), PATHINFO_FILENAME);

        $fieldMappings = $index->dataSource->field_mappings;

        $companies = Collection::make();
        $marketData = Collection::make();

        $this->preloadEntities();

        foreach ($jsonData['aaData'] as $holding) {
            $ticker = $holding[$fieldMappings['symbol']];
            $companyName = $holding[$fieldMappings['name']];
            $sectorName = $holding[$fieldMappings['sector']];
            $assetClassName = $holding[$fieldMappings['asset_type']];
            $marketCapRaw = $holding[$fieldMappings['market_cap']]['raw'] ?? 0;
            $weightRaw = $holding[$fieldMappings['weight_percentage']]['raw'] ?? 0;
            $isin = $holding[$fieldMappings['isin']];
            $sharePrice = $holding[$fieldMappings['share_price']]['raw'] ?? 0;
            $countryName = $holding[$fieldMappings['country']];
            $exchangeName = $holding[$fieldMappings['exchange']];
            $currencyName = $holding[$fieldMappings['currency']];

            if ($assetClassName !== 'Equity') {
                continue;
            }

            if (empty($isin) || $isin === '-') {
                Log::info("Skipping company '$companyName' - no ISIN provided");

                continue;
            }

            $sectorId = $this->entityResolver->resolve(Sector::class, $sectorName);
            $countryId = $this->entityResolver->resolve(Country::class, $countryName);
            $exchangeId = $this->entityResolver->resolve(Exchange::class, $exchangeName);
            $currencyId = $this->entityResolver->resolve(Currency::class, $currencyName);
            $assetClassId = $this->entityResolver->resolve(AssetClass::class, $assetClassName);

            $companies->push(
                new CompanyData(
                    isin: $isin,
                    ticker: $ticker,
                    name: $companyName,
                    sectorId: $sectorId,
                    countryId: $countryId,
                    exchangeId: $exchangeId,
                    currencyId: $currencyId,
                    assetClassId: $assetClassId,
                )
            );

            $marketData->push(
                new MarketDataPoint(
                    companyIsin: $isin, // temporary reference for IndexHolding lookup
                    marketCapitalization: max($marketCapRaw * 1000, 0),
                    weight: max($weightRaw / 100, 0),
                    sharePrice: max($sharePrice, 0),
                    date: $dateFromFilename,
                )
            );
        }

        return [$companies, $marketData];
    }

    private function preloadEntities(): void
    {
        $this->entityResolver->preload(Sector::class, Sector::withoutGlobalScopes()->pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Country::class, Country::withoutGlobalScopes()->pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Exchange::class, Exchange::pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Currency::class, Currency::pluck('id', 'name')->toArray());
        $this->entityResolver->preload(AssetClass::class, AssetClass::pluck('id', 'name')->toArray());
    }
}
