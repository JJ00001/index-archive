<?php

use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('filters index holdings to only show rows marked as active', function () {
    $activeCompany = Company::factory()->create();
    $inactiveCompany = Company::factory()->create();

    $indexProvider = IndexProvider::factory()->create();

    $index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);

    $activeIndexHolding = IndexHolding::create([
        'company_id' => $activeCompany->id,
        'index_id' => $index->id,
        'is_active' => true,
    ]);

    IndexHolding::create([
        'company_id' => $inactiveCompany->id,
        'index_id' => $index->id,
        'is_active' => false,
    ]);

    $activeHoldings = IndexHolding::all();

    $allHoldings = IndexHolding::withoutGlobalScopes()->get();

    expect($allHoldings)->toHaveCount(2);
    expect($activeHoldings)->toHaveCount(1);
    expect($activeHoldings->first()->id)->toBe($activeIndexHolding->id);
});

