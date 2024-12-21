<?php

namespace App\Http\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompanyLogoService
{
    protected string $logoUrl;
    protected Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function fetchLogo(): ?string
    {
        if (
            $this->searchByTicker()
            || $this->searchByTickerWithExchangeCode()
            || $this->searchByName()
            || $this->searchByNameParts()
        ) {
            return $this->logoUrl;
        }

        Log::error("No logo found: {$this->company->name} ({$this->company->ticker})");

        return null;
    }

    protected function searchByTicker(): bool
    {
        $results = $this->makeApiRequest(['ticker' => $this->company->ticker]);

        return $this->processResults($results);
    }

    protected function makeApiRequest($params): array
    {
        $response = Http::withHeader('X-Api-Key', env('API_NINJA_API_KEY'))
            ->accept('application/json')
            ->get('https://api.api-ninjas.com/v1/logo?', $params);

        return $response->successful() ? $response->json() : [];
    }

    protected function processResults($results): bool
    {
        if (empty($results)) {
            return false;
        }

        foreach ($results as $company) {
            if ($this->compareTickers($company) || $this->compareNames($company)) {
                Log::info("Result confirmed");

                $this->logoUrl = $company['image'];

                return true;
            }

            Log::warning("Result rejected");
        }

        return false;
    }

    protected function compareTickers($company): bool
    {
        $dbTicker = $this->normalizeCompanyTicker($this->company->ticker);
        $apiTicker = $this->normalizeCompanyTicker($company['ticker']);

        Log::info("Comparing normalized tickers: $apiTicker vs. $dbTicker");

        return $apiTicker === $dbTicker;
    }

    protected function normalizeCompanyTicker(string $ticker): string
    {
        $exchangeSuffixes = array_values($this->getExchangeCodes());

        foreach ($exchangeSuffixes as $suffix) {
            if (str_ends_with($ticker, $suffix)) {
                $ticker = substr($ticker, 0, -strlen($suffix));

                break;
            }
        }

        return $ticker;
    }

    protected function getExchangeCodes(): array
    {
        return [
            'NASDAQ' => '.O',
            'New York Stock Exchange Inc.' => '.N',
            'Six Swiss Exchange Ag' => '.SW',
            'London Stock Exchange' => '.L',
            'Tokyo Stock Exchange' => '.T',
            'Nyse Euronext - Euronext Paris' => '.PA',
            'Toronto Stock Exchange' => '.TO',
            'Xetra' => '.DE',
            'Bolsa De Madrid' => '.MC',
            'Asx - All Markets' => '.AX',
            'Hong Kong Exchanges And Clearing Ltd' => '.HK',
            'Omx Nordic Exchange Copenhagen A/S' => '.CO',
            'Euronext Amsterdam' => '.AS',
            'Nyse Euronext - Euronext Brussels' => '.BR',
            'Borsa Italiana' => '.MI',
            'Nasdaq Omx Nordic' => '.ST',
            'Singapore Exchange' => '.SI',
            'Irish Stock Exchange - All Market' => '.IR',
            'Nasdaq Omx Helsinki Ltd.' => '.HE',
            'Oslo Bors Asa' => '.OL',
            'Tel Aviv Stock Exchange' => '.TA',
            'Wiener Boerse Ag' => '.VI',
            'Nyse Mkt Llc' => '.MKT',
            'Nyse Euronext - Euronext Lisbon' => '.LS',
            'SIX Swiss Exchange' => '.SW',
            'Deutsche Börse AG' => '.DE',
            'New Zealand Exchange Ltd' => '.NZ',
            'Cboe BZX formerly known as BATS' => '.BZ',
            'Bme Bolsas Y Mercados Espanoles' => '.BM',
            'NYSE Arca' => '.ARCA',
        ];
    }

    protected function compareNames($company): bool
    {
        $dbName = $this->normalizeCompanyName($this->company->name);
        $apiName = $this->normalizeCompanyName($company['name']);

        return levenshtein($dbName, $apiName) <= 3;
    }

    protected function normalizeCompanyName($name): string
    {
        $name = strtolower($name);

        $suffixes = $this->getCommonCompanySuffixes();

        foreach ($suffixes as $suffix) {
            $name = str_replace(' ' . $suffix, '', $name);
        }

        return trim($name);
    }

    protected function getCommonCompanySuffixes(): array
    {
        return [
            'inc',
            'incorporated',
            'corp',
            'corporation',
            'ltd',
            'llc',
            'limited',
            'co',
            'company',
            'ag',
            'class a',
            'class b',
            'class c',
            'plc',
            'sa',
        ];
    }

    protected function searchByTickerWithExchangeCode(): bool
    {
        $exchangeCode = $this->findExchangeCode($this->company->exchange->name);

        if (!$exchangeCode) {
            return false;
        }

        $tickerWithExchange = $this->company->ticker . $exchangeCode;

        $results = $this->makeApiRequest(['ticker' => $tickerWithExchange]);

        return $this->processResults($results);
    }

    protected function findExchangeCode($exchangeName): ?string
    {
        $exchangeCodes = $this->getExchangeCodes();

        return $exchangeCodes[$exchangeName] ?? null;
    }

    protected function searchByName(): bool
    {
        $results = $this->makeApiRequest(['name' => $this->company->name]);

        return $this->processResults($results);
    }

    protected function searchByNameParts(): bool
    {
        $nameParts = explode(' ', $this->company->name);

        foreach ($nameParts as $part) {
            $results = $this->makeApiRequest(['name' => $part]);

            if ($this->processResults($results)) {
                return true;
            }
        }

        return false;
    }

    public function storeLogo(): void
    {
        $response = Http::get($this->logoUrl);
        $logo = $response->body();
        $logoPathInStorage = 'logos/' . $this->company->isin . '.png';
        Storage::disk('public')->put($logoPathInStorage, $logo);
        $this->company->update(['logo' => $logoPathInStorage]);

        Log::info("Logo stored for: {$this->company->name} ({$this->company->ticker})");
    }
}
