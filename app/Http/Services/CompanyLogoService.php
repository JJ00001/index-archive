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
            if ($this->isDesiredCompany($company)) {
                $this->logoUrl = $company['image'];

                return true;
            }
        }

        return false;
    }

    protected function isDesiredCompany($company): bool
    {
        $dbName = $this->normalizeCompanyName($this->company->name);
        $apiName = $this->normalizeCompanyName($company['name']);

        return levenshtein($company['ticker'], $this->company->ticker) <= 3
            && (
                $dbName === $apiName
                || str_contains($apiName, $dbName)
                || str_contains($dbName, $apiName)
            );
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

    public function getCommonCompanySuffixes(): array
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
        $exchangeCode = $this->getExchangeCode($this->company->exchange->name);

        if (!$exchangeCode) {
            return false;
        }

        $tickerWithExchange = $this->company->ticker . $exchangeCode;

        $results = $this->makeApiRequest(['ticker' => $tickerWithExchange]);

        return $this->processResults($results);
    }

    protected function getExchangeCode($exchangeName): ?string
    {
        $exchangeCodes = [
            'Tokyo Stock Exchange' => '.T',
            'London Stock Exchange' => '.L',
            'Nyse Euronext - Euronext Paris' => '.PA',
            'Toronto Stock Exchange' => '.TO',
            'Xetra' => '.DE',
            'Bolsa De Madrid' => '.MC',
            'Asx - All Markets' => '.AX',
            'Hong Kong Exchanges And Clearing Ltd' => '.HK',
            'Omx Nordic Exchange Copenhagen A/S' => '.VI',
            'Euronext Amsterdam' => '.AS',
            'Nasdaq Omx Nordic' => '.ST',
        ];

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
        $logoPathInStorage = 'logos/' . $this->company->ticker . '.png';
        Storage::disk('public')->put($logoPathInStorage, $logo);
        $this->company->update(['logo' => $logoPathInStorage]);

        Log::info("Logo stored for: {$this->company->name} ({$this->company->ticker})");
    }
}
