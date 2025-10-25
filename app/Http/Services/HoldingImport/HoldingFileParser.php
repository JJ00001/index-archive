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
    private array $seenIsins = [];

    public function __construct(private EntityResolver $entityResolver) {}

    /**
     * @throws JsonException
     */
    public function parse(Index $index, string $fullFilePath): array
    {
        $this->seenIsins = [];

        [$jsonData, $fieldMappings, $date] = $this->loadFile($index, $fullFilePath);

        $companiesFromFile = Collection::make();
        $marketDataFromFile = Collection::make();

        $this->preloadEntities();

        foreach ($jsonData['aaData'] as $holding) {
            $fields = $this->extractHoldingFields($holding, $fieldMappings);

            if ($this->shouldSkipHolding($fields)) {
                continue;
            }

            $this->seenIsins[$fields['isin']] = true;

            $entityIds = $this->resolveEntityIds($fields);

            $companiesFromFile->push($this->createCompanyData($fields, $entityIds));
            $marketDataFromFile->push($this->createMarketDataPoint($fields, $date));
        }

        return [$companiesFromFile, $marketDataFromFile];
    }

    private function preloadEntities(): void
    {
        $this->entityResolver->preload(Sector::class, Sector::withoutGlobalScopes()->pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Country::class, Country::withoutGlobalScopes()->pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Exchange::class, Exchange::pluck('id', 'name')->toArray());
        $this->entityResolver->preload(Currency::class, Currency::pluck('id', 'name')->toArray());
        $this->entityResolver->preload(AssetClass::class, AssetClass::pluck('id', 'name')->toArray());
    }

    private function loadFile(Index $index, string $fullFilePath): array
    {
        $fileContent = file_get_contents($fullFilePath);
        $jsonData = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        $date = pathinfo(basename($fullFilePath), PATHINFO_FILENAME);
        $fieldMappings = $index->dataSource->field_mappings;

        return [$jsonData, $fieldMappings, $date];
    }

    private function extractHoldingFields(array $holding, array $fieldMappings): array
    {
        return [
            'ticker' => $holding[$fieldMappings['symbol']],
            'companyName' => $holding[$fieldMappings['name']],
            'sectorName' => $holding[$fieldMappings['sector']],
            'assetClassName' => $holding[$fieldMappings['asset_type']],
            'marketCapRaw' => $holding[$fieldMappings['market_cap']]['raw'] ?? 0,
            'weightRaw' => $holding[$fieldMappings['weight_percentage']]['raw'] ?? 0,
            'isin' => $holding[$fieldMappings['isin']],
            'sharePrice' => $holding[$fieldMappings['share_price']]['raw'] ?? 0,
            'countryName' => $holding[$fieldMappings['country']],
            'exchangeName' => $holding[$fieldMappings['exchange']],
            'currencyName' => $holding[$fieldMappings['currency']],
        ];
    }

    private function shouldSkipHolding(array $fields): bool
    {
        if ($fields['assetClassName'] !== 'Equity') {
            return true;
        }

        if (empty($fields['isin']) || $fields['isin'] === '-') {
            Log::info("Skipping company '{$fields['companyName']}' - no ISIN provided");

            return true;
        }

        if (isset($this->seenIsins[$fields['isin']])) {
            Log::info("Skipping duplicate ISIN '{$fields['isin']}' for company '{$fields['companyName']}'");

            return true;
        }

        return false;
    }

    private function resolveEntityIds(array $fields): array
    {
        return [
            'sectorId' => $this->entityResolver->resolve(Sector::class, $fields['sectorName']),
            'countryId' => $this->entityResolver->resolve(Country::class, $fields['countryName']),
            'exchangeId' => $this->entityResolver->resolve(Exchange::class, $fields['exchangeName']),
            'currencyId' => $this->entityResolver->resolve(Currency::class, $fields['currencyName']),
            'assetClassId' => $this->entityResolver->resolve(AssetClass::class, $fields['assetClassName']),
        ];
    }

    private function createCompanyData(array $fields, array $entityIds): CompanyData
    {
        return new CompanyData(
            isin: $fields['isin'],
            ticker: $fields['ticker'],
            name: $fields['companyName'],
            sectorId: $entityIds['sectorId'],
            countryId: $entityIds['countryId'],
            exchangeId: $entityIds['exchangeId'],
            currencyId: $entityIds['currencyId'],
            assetClassId: $entityIds['assetClassId'],
        );
    }

    private function createMarketDataPoint(array $fields, string $date): MarketDataPoint
    {
        return new MarketDataPoint(
            companyIsin: $fields['isin'],
            marketCapitalization: max($fields['marketCapRaw'] * 1000, 0),
            weight: max($fields['weightRaw'] / 100, 0),
            sharePrice: max($fields['sharePrice'], 0),
            date: $date,
        );
    }
}
