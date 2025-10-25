<?php

use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('filters index holdings to only show active ones based on market data max date', function () {
    $activeCompany = Company::factory()->create();
    $inactiveCompany = Company::factory()->create();

    $indexProvider = IndexProvider::factory()->create();

    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $activeIndexHolding = IndexHolding::create(['company_id' => $activeCompany->id, 'index_id' => $index->id]);
    $inactiveIndexHolding = IndexHolding::create(['company_id' => $inactiveCompany->id, 'index_id' => $index->id]);

    $maxDate = '2024-01-15';
    $olderDate = '2024-01-10';

    MarketData::factory()->create([
        'index_holding_id' => $activeIndexHolding->id,
        'date' => $maxDate,
    ]);

    MarketData::factory()->create([
        'index_holding_id' => $inactiveIndexHolding->id,
        'date' => $olderDate,
    ]);

    $activeHoldings = IndexHolding::all();

    $allHoldings = IndexHolding::withoutGlobalScopes()->get();

    expect($allHoldings)->toHaveCount(2);
    expect($activeHoldings)->toHaveCount(1);
    expect($activeHoldings->first()->id)->toBe($activeIndexHolding->id);
});

it('should include index A active holding even when index B has newer max date', function () {
    $indexACompany = Company::factory()->create();
    $indexBCompany = Company::factory()->create();

    $indexProvider = IndexProvider::factory()->create();

    $indexA = Index::factory()->create(['index_provider_id' => $indexProvider->id]);
    $indexB = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $indexAHolding = IndexHolding::create([
        'company_id' => $indexACompany->id,
        'index_id' => $indexA->id,
    ]);
    $indexBHolding = IndexHolding::create([
        'company_id' => $indexBCompany->id,
        'index_id' => $indexB->id,
    ]);

    $indexAMaxDate = '2024-01-15';
    $indexBMaxDate = '2024-01-20'; // Becomes the new global max

    MarketData::factory()->create([
        'index_holding_id' => $indexAHolding->id,
        'date' => $indexAMaxDate,
    ]);

    MarketData::factory()->create([
        'index_holding_id' => $indexBHolding->id,
        'date' => $indexBMaxDate,
    ]);

    $indexAActiveHoldings = IndexHolding::where('index_id', $indexA->id)->get();

    expect($indexAActiveHoldings)->toHaveCount(1);
    expect($indexAActiveHoldings->first()->id)->toBe($indexAHolding->id);
});
