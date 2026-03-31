<?php

use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shares the latest market data date with the index show page', function () {
    $indexProvider = IndexProvider::factory()->create();
    $index         = Index::factory()->create([
        'name' => 'MSCI World',
        'index_provider_id' => $indexProvider->id,
    ]);
    $company       = Company::factory()->create();
    $indexHolding  = IndexHolding::factory()->create([
        'index_id' => $index->id,
        'company_id' => $company->id,
        'is_active' => true,
    ]);

    MarketData::factory()->create([
        'index_holding_id' => $indexHolding->id,
        'date' => '2026-02-28',
    ]);

    MarketData::factory()->create([
        'index_holding_id' => $indexHolding->id,
        'date' => '2026-03-31',
    ]);

    $response = $this->get(route('indices.show', $index));

    $response->assertSuccessful();
    expect($response->inertiaProps('dataUpdatedAt'))->toBe('2026-03-31');
});
