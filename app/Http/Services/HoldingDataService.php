<?php

namespace App\Http\Services;

use App\Models\AssetClass;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Exchange;
use App\Models\MarketData;
use App\Models\Sector;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HoldingDataService
{
    public function writeHoldingDataToDB(string $filename)
    {
        $directoryPath = storage_path('holdingsData/');
        $filePath = glob($directoryPath . $filename)[0];
        $fileContent = file_get_contents($filePath);
        $jsonData = json_decode($fileContent, true);
        $dateFromFilename = pathinfo(basename($filename), PATHINFO_FILENAME);

        foreach ($jsonData['aaData'] as $data) {
            $ticker = $data[0];
            $companyName = $data[1];
            $sector = $data[2];
            $assetClass = $data[3];
            $marketCap = $data[4]['raw'];
            $weight = $data[5]['raw'];
            $isin = $data[8];
            $sharePrice = $data[9]['raw'];
            $country = $data[10];
            $exchange = $data[11];
            $currency = $data[12];

            if ($assetClass === 'Aktien') {
                // Attributes beside ISIN might change
                $company = Company::updateOrCreate(['isin' => $isin], [
                    'ticker' => $ticker,
                    'name' => $companyName,
                    'sector_id' => Sector::firstOrCreate(['name' => $sector])->id,
                    'country_id' => Country::firstOrCreate(['name' => $country])->id,
                    'exchange_id' => Exchange::firstOrCreate(['name' => $exchange])->id,
                    'currency_id' => Currency::firstOrCreate(['name' => $currency])->id,
                    'asset_class_id' => AssetClass::firstOrCreate(['name' => $assetClass])->id,
                ]);

                $companyLogoExists = (bool)$company->logo;

                if ($company->wasRecentlyCreated && !$companyLogoExists) {
                    try {
                        $response = Http::withHeader('X-Api-Key', env('API_NINJA_API_KEY'))
                            ->accept('application/json')
                            ->get('https://api.api-ninjas.com/v1/logo?ticker=' . $company->ticker);

                        $logoURL = $response->json()[0]['image'] ?? null;

                        if ($logoURL) {
                            $response = Http::get($logoURL);

                            $logo = $response->body();

                            $logoPathInStorage = 'logos/' . $company->ticker . '.png';

                            Storage::disk('public')->put($logoPathInStorage, $logo);

                            $company->update(['logo' => $logoPathInStorage]);
                        }
                    } catch (ConnectionException $e) {
                        Log::info($e->getMessage());
                    }
                }

                MarketData::create([
                    'company_id' => $company->id,
                    'market_capitalization' => max($marketCap, 0),
                    'weight' => max($weight, 0),
                    'share_price' => max($sharePrice, 0),
                    'date' => $dateFromFilename,
                ]);
            }
        }
    }
}
