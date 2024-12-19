<?php

namespace App\Http\Services;

use App\Jobs\FetchCompanyLogo;
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
            // Raw market capitalization value is in thousands. Multiply by 1000 to convert it to the actual value.
            $marketCap = $data[4]['raw'] * 1000;
            // Raw weight value is in percentage. Divide by 100 to convert it decimal representation.
            $weight = $data[5]['raw'] / 100;
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
