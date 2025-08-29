<?php

namespace App\Http\Services;

use App\Models\AssetClass;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\Index;
use App\Models\MarketData;
use App\Models\Sector;
use DateMalformedStringException;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use JsonException;
use Random\RandomException;

class HoldingDataService
{
    /**
     * @throws DateMalformedStringException
     * @throws GuzzleException
     * @throws RandomException
     * @throws JsonException
     */
    public function scrape(Index $index, DateTime $startDate, DateTime $endDate)
    {
        $baseURL = $index->dataSources()->first()->base_url;
        $date = $startDate;
        $client = new Client();

        while ($date <= $endDate) {
            $dateFormatted = $date->format('Y-m-d');
            $randomDelay = random_int(1000, 3000);

            $url = $baseURL . $date->format('Ymd');
            $response = $client->request('GET', $url, ['delay' => $randomDelay]);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $filename = storage_path('holdingsData/' . $dateFormatted . '.json');
                $response = $response->getBody()->getContents();

                $response = str_replace("\u{FEFF}", '', $response);
                $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

                Log::info('Scraping complete for: ' . $dateFormatted);

                if (!empty($response['aaData'])) {
                    $jsonData = json_encode($response, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                    file_put_contents($filename, $jsonData);
                } else {
                    Log::info('No data for: ' . $dateFormatted);

                    $date = $date->modify('next day');
                    continue;
                }
            } else {
                Log::info('Request failed with status code: ' . $statusCode);
            }

            $date = $date->modify('first day of next month');
        }
    }

    /**
     * @throws JsonException
     */
    public function writeHoldingDataToDB(string $filename)
    {
        $directoryPath = storage_path('holdingsData/');
        $filePath = glob($directoryPath . $filename)[0];
        $fileContent = file_get_contents($filePath);
        $jsonData = json_decode($fileContent, true, 512, JSON_THROW_ON_ERROR);
        $dateFromFilename = pathinfo(basename($filename), PATHINFO_FILENAME);

        $companies = [];
        $marketData = [];

        $sectors = Sector::withoutGlobalScopes()->pluck('id', 'name');
        $countries = Country::withoutGlobalScopes()->pluck('id', 'name');
        $exchanges = Exchange::pluck('id', 'name');
        $currencies = Currency::pluck('id', 'name');
        $assetClasses = AssetClass::pluck('id', 'name');

        foreach ($jsonData['aaData'] as $holding) {
            $ticker = $holding[0];
            $companyName = $holding[1];
            $sectorName = $holding[2];
            $assetClassName = $holding[3];
            $marketCapRaw = $holding[4]['raw'] ?? 0;
            $weightRaw = $holding[5]['raw'] ?? 0;
            $isin = $holding[8];
            $sharePriceRaw = $holding[9]['raw'] ?? 0;
            $countryName = $holding[10];
            $exchangeName = $holding[11];
            $currencyName = $holding[12];

            if ($assetClassName === 'Aktien') {
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
                    'company_id' => $isin, // temporary reference
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

        Log::info('Companies upserted. Resolving Market Data IDs...');

        $companyIds = Company::withoutGlobalScopes()
            ->whereIn('isin', array_column($companies, 'isin'))
            ->pluck('id', 'isin');

        foreach ($marketData as &$data) {
            $isin = $data['company_id'];
            if (isset($companyIds[$isin])) {
                $data['company_id'] = $companyIds[$isin];
            } else {
                // If we don't find a matching company, log it or handle as needed
                Log::warning('No matching company found for ISIN: ' . $isin);
            }
        }

        unset($data);

        Log::info('Inserting Market Data...');

        MarketData::insert($marketData);

        Log::info('Data processing complete!');
    }
}
