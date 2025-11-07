<?php

use App\Http\Resources\IndexHoldingCountryCollection;
use App\Models\Company;
use App\Models\Country;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('calculates correct country weights from market data', function () {
    $indexProvider = IndexProvider::factory()->create();
    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $usCountry = Country::factory()->create(['name' => 'United States']);
    $jpCountry = Country::factory()->create(['name' => 'Japan']);

    $usCompany = Company::factory()->create(['country_id' => $usCountry->id]);
    $jpCompany = Company::factory()->create(['country_id' => $jpCountry->id]);

    $usHolding = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $usCompany->id,
        'is_active' => true,
    ]);
    $jpHolding = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $jpCompany->id,
        'is_active' => true,
    ]);

    $date = '2025-01-15';
    MarketData::factory()->create(['index_holding_id' => $usHolding->id, 'date' => $date, 'weight' => 0.45]);
    MarketData::factory()->create(['index_holding_id' => $jpHolding->id, 'date' => $date, 'weight' => 0.30]);

    $collection = new IndexHoldingCountryCollection($index);

    expect($collection->collection)->toHaveCount(2);

    $countries = $collection->collection->sortByDesc('weight');
    expect($countries->first()->weight)->toBe(0.45);
    expect($countries->first()->name)->toBe('United States');
    expect($countries->last()->weight)->toBe(0.30);
    expect($countries->last()->name)->toBe('Japan');
});

it('counts companies per country correctly', function () {
    $indexProvider = IndexProvider::factory()->create();
    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $usCountry = Country::factory()->create(['name' => 'United States']);
    $usCompany1 = Company::factory()->create(['country_id' => $usCountry->id]);
    $usCompany2 = Company::factory()->create(['country_id' => $usCountry->id]);

    $holding1 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $usCompany1->id,
        'is_active' => true,
    ]);
    $holding2 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $usCompany2->id,
        'is_active' => true,
    ]);

    $date = '2025-01-15';
    MarketData::factory()->create(['index_holding_id' => $holding1->id, 'date' => $date, 'weight' => 0.25]);
    MarketData::factory()->create(['index_holding_id' => $holding2->id, 'date' => $date, 'weight' => 0.20]);

    $collection = new IndexHoldingCountryCollection($index);

    expect($collection->collection)->toHaveCount(1);
    expect($collection->collection->first()->companies_count)->toBe(2);
});

it('returns countries sorted by weight descending', function () {
    $indexProvider = IndexProvider::factory()->create();
    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $country1 = Country::factory()->create(['name' => 'Germany']);
    $country2 = Country::factory()->create(['name' => 'France']);
    $country3 = Country::factory()->create(['name' => 'United Kingdom']);

    $company1 = Company::factory()->create(['country_id' => $country1->id]);
    $company2 = Company::factory()->create(['country_id' => $country2->id]);
    $company3 = Company::factory()->create(['country_id' => $country3->id]);

    $holding1 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $company1->id,
        'is_active' => true,
    ]);
    $holding2 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $company2->id,
        'is_active' => true,
    ]);
    $holding3 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $company3->id,
        'is_active' => true,
    ]);

    $date = '2025-01-15';
    MarketData::factory()->create(['index_holding_id' => $holding1->id, 'date' => $date, 'weight' => 0.10]);
    MarketData::factory()->create(['index_holding_id' => $holding2->id, 'date' => $date, 'weight' => 0.50]);
    MarketData::factory()->create(['index_holding_id' => $holding3->id, 'date' => $date, 'weight' => 0.30]);

    $collection = new IndexHoldingCountryCollection($index);

    $countries = $collection->collection;
    expect($countries[0]->name)->toBe('France');
    expect($countries[0]->weight)->toBe(0.50);
    expect($countries[1]->name)->toBe('United Kingdom');
    expect($countries[1]->weight)->toBe(0.30);
    expect($countries[2]->name)->toBe('Germany');
    expect($countries[2]->weight)->toBe(0.10);
});

it('handles multiple companies per country with multiple market data points', function () {
    $indexProvider = IndexProvider::factory()->create();
    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $country = Country::factory()->create(['name' => 'Switzerland']);
    $company1 = Company::factory()->create(['country_id' => $country->id]);
    $company2 = Company::factory()->create(['country_id' => $country->id]);

    $holding1 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $company1->id,
        'is_active' => true,
    ]);
    $holding2 = IndexHolding::create([
        'index_id' => $index->id,
        'company_id' => $company2->id,
        'is_active' => true,
    ]);

    $date = '2025-01-15';
    MarketData::factory()->create(['index_holding_id' => $holding1->id, 'date' => $date, 'weight' => 0.35]);
    MarketData::factory()->create(['index_holding_id' => $holding2->id, 'date' => $date, 'weight' => 0.15]);

    $collection = new IndexHoldingCountryCollection($index);

    expect($collection->collection)->toHaveCount(1);
    expect($collection->collection->first()->companies_count)->toBe(2);
    expect($collection->collection->first()->weight)->toBe(0.50);
});
