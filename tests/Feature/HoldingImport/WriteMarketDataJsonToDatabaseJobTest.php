<?php

use App\Jobs\WriteMarketDataJsonToDatabase;
use App\Models\AssetClass;
use App\Models\Company;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DataSource;
use App\Models\Exchange;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;

uses(RefreshDatabase::class);

beforeEach(function () {
    $indexProvider = IndexProvider::factory()->create();
    $this->index = Index::factory()->create(['id' => 1, 'index_provider_id' => $indexProvider->id]);
    DataSource::factory()->create(['index_id' => $this->index->id]);

    $this->compactTestFilePath = base_path('tests/Fixtures/holdingsData/1/2025-01-01.json');
    $this->fullTestFilePath = base_path('tests/Fixtures/holdingsData/1/2025-08-01.json');
});

describe('Job Execution', function () {
    it('dispatches and handles the job successfully', function () {
        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);

        expect($job->fullFilePath)->toBe($this->compactTestFilePath);

        $job->handle();

        assertDatabaseCount('companies', 2);
        assertDatabaseHas('companies', ['ticker' => 'NVDA', 'name' => 'NVIDIA CORP']);
        assertDatabaseHas('companies', ['ticker' => 'MSFT', 'name' => 'MICROSOFT CORP']);

        assertDatabaseCount('index_holdings', 2);

        assertDatabaseCount('market_data', 2);
    });

    it('throws JsonException for invalid JSON', function () {
        $invalidJsonPath = base_path('tests/Fixtures/holdingsData/1/invalid.json');
        file_put_contents($invalidJsonPath, 'invalid json content');

        $job = new WriteMarketDataJsonToDatabase($invalidJsonPath);

        expect(fn () => $job->handle())->toThrow(JsonException::class);

        unlink($invalidJsonPath);
    });
});

describe('Data Processing', function () {
    it('correctly extracts index ID from file path', function () {
        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('index_holdings', 2);
        assertDatabaseHas('index_holdings', ['index_id' => $this->index->id, 'is_active' => true]);
    });

    it('creates new companies from JSON data', function () {
        assertDatabaseCount('companies', 0);

        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('companies', 2);

        $nvidia = Company::where('ticker', 'NVDA')->first();
        expect($nvidia)->not()->toBeNull();
        expect($nvidia->name)->toBe('NVIDIA CORP');
        expect($nvidia->isin)->toBe('US67066G1040');

        $microsoft = Company::where('ticker', 'MSFT')->first();
        expect($microsoft)->not()->toBeNull();
        expect($microsoft->name)->toBe('MICROSOFT CORP');
        expect($microsoft->isin)->toBe('US5949181045');
    });

    it('updates existing companies when data changes', function () {
        $sector = Sector::factory()->create(['name' => 'Old Sector']);
        $country = Country::factory()->create(['name' => 'Old Country']);
        $exchange = Exchange::factory()->create(['name' => 'Old Exchange']);
        $currency = Currency::factory()->create(['name' => 'OLD']);
        $assetClass = AssetClass::factory()->create(['name' => 'Old Asset']);

        Company::factory()->create([
            'ticker' => 'NVDA',
            'name' => 'OLD NVIDIA NAME',
            'isin' => 'US67066G1040',
            'sector_id' => $sector->id,
            'country_id' => $country->id,
            'exchange_id' => $exchange->id,
            'currency_id' => $currency->id,
            'asset_class_id' => $assetClass->id,
        ]);

        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        $nvidia = Company::where('isin', 'US67066G1040')->first();
        expect($nvidia->name)->toBe('NVIDIA CORP');
        expect($nvidia->ticker)->toBe('NVDA');
        expect($nvidia->sector->name)->toBe('Information Technology');
        expect($nvidia->country->name)->toBe('United States');
        expect($nvidia->exchange->name)->toBe('NASDAQ');
        expect($nvidia->currency->name)->toBe('USD');
        expect($nvidia->assetClass->name)->toBe('Equity');
    });

    it('skips company updates when no changes detected', function () {
        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        $initialUpdatedAt = Company::where('ticker', 'NVDA')->first()->updated_at;

        $job->handle();

        $finalUpdatedAt = Company::where('ticker', 'NVDA')->first()->updated_at;
        expect($finalUpdatedAt->timestamp)->toBe($initialUpdatedAt->timestamp);
    });

    it('creates reference entities (sectors, countries, exchanges, currencies, asset classes)', function () {
        assertDatabaseCount('sectors', 0);
        assertDatabaseCount('countries', 0);
        assertDatabaseCount('exchanges', 0);
        assertDatabaseCount('currencies', 0);
        assertDatabaseCount('asset_classes', 0);

        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('sectors', 1);
        assertDatabaseHas('sectors', ['name' => 'Information Technology']);

        assertDatabaseCount('countries', 1);
        assertDatabaseHas('countries', ['name' => 'United States']);

        assertDatabaseCount('exchanges', 1);
        assertDatabaseHas('exchanges', ['name' => 'NASDAQ']);

        assertDatabaseCount('currencies', 1);
        assertDatabaseHas('currencies', ['name' => 'USD']);

        assertDatabaseCount('asset_classes', 1);
        assertDatabaseHas('asset_classes', ['name' => 'Equity']);
    });
});

describe('Relationship Management', function () {
    it('creates IndexHolding records for new companies', function () {
        assertDatabaseCount('index_holdings', 0);

        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('index_holdings', 2);

        $nvidia = Company::where('ticker', 'NVDA')->first();
        $microsoft = Company::where('ticker', 'MSFT')->first();

        assertDatabaseHas('index_holdings', [
            'index_id' => $this->index->id,
            'company_id' => $nvidia->id,
            'is_active' => true,
        ]);

        assertDatabaseHas('index_holdings', [
            'index_id' => $this->index->id,
            'company_id' => $microsoft->id,
            'is_active' => true,
        ]);
    });

    it('avoids duplicate IndexHolding records', function () {
        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('index_holdings', 2);

        $job->handle();

        assertDatabaseCount('index_holdings', 2);
    });

    it('links MarketData to correct IndexHolding records', function () {
        $job = new WriteMarketDataJsonToDatabase($this->compactTestFilePath);
        $job->handle();

        assertDatabaseCount('market_data', 2);

        $nvidia = Company::where('ticker', 'NVDA')->first();
        $nvidiaHolding = IndexHolding::where('company_id', $nvidia->id)->first();

        $nvidiaMarketData = MarketData::where('index_holding_id', $nvidiaHolding->id)->first();
        expect($nvidiaMarketData)->not()->toBeNull();
        expect($nvidiaMarketData->weight)->toBe(0.08004);
        expect($nvidiaMarketData->share_price)->toBe(173.72);
        expect($nvidiaMarketData->date)->toBe('2025-01-01');

        $microsoft = Company::where('ticker', 'MSFT')->first();
        $microsoftHolding = IndexHolding::where('company_id', $microsoft->id)->first();

        $microsoftMarketData = MarketData::where('index_holding_id', $microsoftHolding->id)->first();
        expect($microsoftMarketData)->not()->toBeNull();
        expect($microsoftMarketData->weight)->toBe(0.07359);
        expect($microsoftMarketData->share_price)->toBe(524.11);
    });
});

describe('Edge Cases', function () {
    it('handles missing or invalid index ID in path', function () {
        $invalidPath = base_path('tests/Fixtures/holdingsData/999/small-sample.json');

        $job = new WriteMarketDataJsonToDatabase($invalidPath);

        expect(fn () => $job->handle())->toThrow(Exception::class);
    });

    it('handles duplicate ISINs in source data by keeping only first occurrence', function () {
        $duplicateIsinData = [
            'aaData' => [
                [
                    'AAPL',
                    'APPLE INC',
                    'Information Technology',
                    'Equity',
                    ['display' => '$3,000,000', 'raw' => 3000000],
                    ['display' => '3.0', 'raw' => 3.0],
                    ['display' => '3,000,000', 'raw' => 3000000],
                    ['display' => '10000', 'raw' => 10000],
                    '037833100',
                    'US0378331005',
                    '2046251',
                    ['display' => '300.0', 'raw' => 300.0],
                    'United States',
                    'NASDAQ',
                    'USD',
                    '1.00',
                    '-',
                ],
                [
                    'AAPL',
                    'APPLE INC',
                    'Information Technology',
                    'Equity',
                    ['display' => '$2,500,000', 'raw' => 2500000],
                    ['display' => '2.5', 'raw' => 2.5],
                    ['display' => '2,500,000', 'raw' => 2500000],
                    ['display' => '8000', 'raw' => 8000],
                    '037833100',
                    'US0378331005',
                    '2046252',
                    ['display' => '312.5', 'raw' => 312.5],
                    'United States',
                    'Deutsche Boerse Xetra',
                    'USD',
                    '0.88',
                    '-',
                ],
            ],
        ];

        $duplicatePath = base_path('tests/Fixtures/holdingsData/1/duplicate-isin.json');
        file_put_contents($duplicatePath, json_encode($duplicateIsinData));

        $job = new WriteMarketDataJsonToDatabase($duplicatePath);
        $job->handle();

        assertDatabaseCount('companies', 1);
        assertDatabaseCount('index_holdings', 1);
        assertDatabaseCount('market_data', 1);

        $apple = Company::where('isin', 'US0378331005')->first();
        expect($apple)->not()->toBeNull();
        expect($apple->ticker)->toBe('AAPL');
        expect($apple->exchange->name)->toBe('NASDAQ');

        expect(Company::where('name', 'APPLE INC')->count())->toBe(1);

        $marketData = MarketData::whereHas('indexHolding', function ($query) use ($apple) {
            $query->where('company_id', $apple->id);
        })->first();
        expect($marketData->weight)->toBe(0.03);
        expect($marketData->share_price)->toBe(300.0);

        unlink($duplicatePath);
    });

    it('filters out non-equity asset classes', function () {
        $nonEquityData = [
            'aaData' => [
                [
                    'BOND123',
                    'Test Bond',
                    'Fixed Income',
                    'Bonds',
                    ['display' => '$1,000,000', 'raw' => 1000000],
                    ['display' => '1.0', 'raw' => 1.0],
                    ['display' => '1,000,000', 'raw' => 1000000],
                    ['display' => '1000', 'raw' => 1000],
                    'BOND123456',
                    'US1234567890',
                    '12345',
                    ['display' => '100.0', 'raw' => 100.0],
                    'United States',
                    'NYSE',
                    'USD',
                    '1.00',
                    '-',
                ],
            ],
        ];

        $nonEquityPath = base_path('tests/Fixtures/holdingsData/1/non-equity.json');
        file_put_contents($nonEquityPath, json_encode($nonEquityData));

        $job = new WriteMarketDataJsonToDatabase($nonEquityPath);
        $job->handle();

        assertDatabaseCount('companies', 0);
        assertDatabaseCount('market_data', 0);

        unlink($nonEquityPath);
    });
});

describe('Integration Test', function () {
    it('processes the full dataset correctly', function () {
        $job = new WriteMarketDataJsonToDatabase($this->fullTestFilePath);
        $job->handle();

        expect(Company::count())->toBe(503);
        expect(MarketData::count())->toBe(503);
        expect(IndexHolding::count())->toBe(503);

        $invalidMarketData = MarketData::whereDoesntHave('indexHolding')->count();
        expect($invalidMarketData)->toBe(0);
    });
});
