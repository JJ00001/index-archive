<?php

namespace App\Http\Services;

use App\Models\AssetClass;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\MarketData;
use App\Models\Sector;
use Illuminate\Support\Facades\Log;
use JsonException;

// TODO Refactor for maintainability, readability
class HoldingDataService
{

    /**
     * @throws JsonException
     */
    public function writeHoldingDataToDB(string $fullFilePath)
    {
        $fileContent      = file_get_contents($fullFilePath);
        $jsonData = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        $dateFromFilename = pathinfo(basename($fullFilePath), PATHINFO_FILENAME);

        $pathParts = explode('/', $fullFilePath);
        $indexId   = $pathParts[array_search('holdingsData', $pathParts) + 1];
        $index     = Index::findOrFail($indexId);

        $fieldMappings = $index->dataSource->field_mappings;

        $companies = [];
        $marketData = [];

        $sectors = Sector::withoutGlobalScopes()->pluck('id', 'name');
        $countries = Country::withoutGlobalScopes()->pluck('id', 'name');
        $exchanges = Exchange::pluck('id', 'name');
        $currencies = Currency::pluck('id', 'name');
        $assetClasses = AssetClass::pluck('id', 'name');

        foreach ($jsonData['aaData'] as $holding) {
            $ticker         = $holding[$fieldMappings['symbol']];
            $companyName    = $holding[$fieldMappings['name']];
            $sectorName     = $holding[$fieldMappings['sector']];
            $assetClassName = $holding[$fieldMappings['asset_type']];
            $marketCapRaw = $holding[$fieldMappings['market_cap']]['raw'] ?? 0;
            $weightRaw      = $holding[$fieldMappings['weight_percentage']]['raw'] ?? 0;
            $isin           = $holding[$fieldMappings['isin']];
            $sharePriceRaw  = $holding[$fieldMappings['share_price']]['raw'] ?? 0;
            $countryName    = $holding[$fieldMappings['country']];
            $exchangeName   = $holding[$fieldMappings['exchange']];
            $currencyName   = $holding[$fieldMappings['currency']];

            if ($assetClassName === 'Equity') {
                if (empty($isin) || $isin === '-') {
                    Log::info("Skipping company '$companyName' - no ISIN provided");
                    continue;
                }

                if (!isset($sectors[$sectorName])) {
                    $newSector = Sector::firstOrCreate(['name' => $sectorName]);
                    $sectors[$sectorName] = $newSector->id;
                }
                $sectorId = $sectors[$sectorName];

                if (!isset($countries[$countryName])) {
                    $newCountry = Country::firstOrCreate(['name' => $countryName]);
                    $countries[$countryName] = $newCountry->id;
                }
                $countryId = $countries[$countryName];

                if (!isset($exchanges[$exchangeName])) {
                    $newExchange = Exchange::firstOrCreate(['name' => $exchangeName]);
                    $exchanges[$exchangeName] = $newExchange->id;
                }
                $exchangeId = $exchanges[$exchangeName];

                if (!isset($currencies[$currencyName])) {
                    $newCurrency = Currency::firstOrCreate(['name' => $currencyName]);
                    $currencies[$currencyName] = $newCurrency->id;
                }
                $currencyId = $currencies[$currencyName];

                if (!isset($assetClasses[$assetClassName])) {
                    $newAssetClass = AssetClass::firstOrCreate(['name' => $assetClassName]);
                    $assetClasses[$assetClassName] = $newAssetClass->id;
                }
                $assetClassId = $assetClasses[$assetClassName];

                $companies[] = [
                    'isin' => $isin,
                    'ticker' => $ticker,
                    'name' => $companyName,
                    'sector_id' => $sectorId,
                    'country_id' => $countryId,
                    'exchange_id' => $exchangeId,
                    'currency_id' => $currencyId,
                    'asset_class_id' => $assetClassId,
                    'updated_at' => now(),
                    'created_at' => now(),
                ];

                $marketData[] = [
                    'company_isin' => $isin, // temporary reference for IndexHolding lookup
                    'market_capitalization' => max($marketCapRaw * 1000, 0),
                    'weight' => max($weightRaw / 100, 0),
                    'share_price' => max($sharePriceRaw, 0),
                    'date' => $dateFromFilename,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Log::info('Upserting companies...');

        $existingCompanies = Company::withoutGlobalScopes()
            ->whereIn('isin', array_column($companies, 'isin'))
            ->get()
            ->keyBy('isin');

        $companiesToUpsert = [];
        $fieldsToCheck = ['ticker', 'name', 'sector_id', 'country_id', 'exchange_id', 'currency_id', 'asset_class_id'];

        foreach ($companies as $companyData) {
            $existing = $existingCompanies[$companyData['isin']] ?? null;
            if (!$existing) {
                $companiesToUpsert[] = $companyData;
            } else {
                $changed = false;
                foreach ($fieldsToCheck as $field) {
                    if ($existing->$field !== $companyData[$field]) {
                        $changed = true;

                        Log::info('Field ' . $field . ' changed for ISIN: ' . $companyData['isin'] . ' (' . $existing->$field . ' -> ' . $companyData[$field] . ')');
                    }
                }

                if ($changed) {
                    $companiesToUpsert[] = $companyData;
                }
            }
        }

        if (!empty($companiesToUpsert)) {
            Log::info('Upserting companies ' . count($companiesToUpsert));
            Company::withoutGlobalScopes()
                ->upsert(
                    $companiesToUpsert,
                    ['isin'],
                    array_merge($fieldsToCheck, ['updated_at'])
                );
        }

        Log::info('Companies upserted. Creating Index Holdings...');

        $companyIds = Company::withoutGlobalScopes()
            ->whereIn('isin', array_column($companies, 'isin'))
            ->pluck('id', 'isin');

        $existingHoldings = IndexHolding::withoutGlobalScopes()
                                        ->where('index_id', $index->id)
                                        ->pluck('company_id')
                                        ->toArray();

        // Create new IndexHolding records only
        $newIndexHoldings = [];
        foreach ($companies as $companyData) {
            $companyId = $companyIds[$companyData['isin']] ?? null;
            if ($companyId && ! in_array($companyId, $existingHoldings)) {
                $newIndexHoldings[] = [
                    'index_id' => $index->id,
                    'company_id' => $companyId,
                ];
            }
        }

        if ( ! empty($newIndexHoldings)) {
            IndexHolding::insert($newIndexHoldings);
        }

        // Get IndexHolding IDs for MarketData
        $indexHoldingIds = IndexHolding::withoutGlobalScopes()
                                       ->where('index_id', $index->id)
                                       ->join('companies', 'companies.id', '=', 'index_holdings.company_id')
                                       ->whereIn('companies.isin', array_column($marketData, 'company_isin'))
                                       ->pluck('index_holdings.id', 'companies.isin');

        foreach ($marketData as &$data) {
            $isin = $data['company_isin'];
            if (isset($indexHoldingIds[$isin])) {
                $data['index_holding_id'] = $indexHoldingIds[$isin];
                unset($data['company_isin']); // Remove temporary reference
            } else {
                Log::warning('No matching index holding found for ISIN: '.$isin);
            }
        }

        unset($data);

        // Filter out market data without valid index_holding_id
        $validMarketData = array_filter($marketData, fn($data) => isset($data['index_holding_id']));

        Log::info('Inserting Market Data...');

        MarketData::insert($validMarketData);

        Log::info('Data processing complete!');
    }
}
