<?php

use App\Http\Services\HoldingImport\IndexHoldingChangeDetector;
use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $indexProvider = IndexProvider::factory()->create();
    $this->index = Index::factory()->create(['index_provider_id' => $indexProvider->id]);
    $this->detector = new IndexHoldingChangeDetector;
});

it('detects no changes when companies remain the same', function () {
    $companies = Company::factory()->count(3)->create();

    foreach ($companies as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    $changes = $this->detector->detectChanges($this->index, $companies);

    expect($changes['additions'])->toHaveCount(0);
    expect($changes['removals'])->toHaveCount(0);
});

it('detects additions when new companies are added', function () {
    $existingCompanies = Company::factory()->count(2)->create();

    foreach ($existingCompanies as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    $newCompanies = Company::factory()->count(2)->create();
    $allCompanies = $existingCompanies->merge($newCompanies);

    $changes = $this->detector->detectChanges($this->index, $allCompanies);

    expect($changes['additions'])->toHaveCount(2);
    expect($changes['additions']->pluck('id')->toArray())
        ->toEqual($newCompanies->pluck('id')->toArray());
    expect($changes['removals'])->toHaveCount(0);
});

it('detects removals when companies are removed from index', function () {
    $allCompanies = Company::factory()->count(4)->create();

    foreach ($allCompanies as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    $remainingCompanies = $allCompanies->take(2);
    $removedCompanies = $allCompanies->skip(2);

    $changes = $this->detector->detectChanges($this->index, $remainingCompanies);

    expect($changes['additions'])->toHaveCount(0);
    expect($changes['removals'])->toHaveCount(2);
    expect($changes['removals']->pluck('id')->sort()->values()->toArray())
        ->toEqual($removedCompanies->pluck('id')->sort()->values()->toArray());
});

it('detects both additions and removals simultaneously', function () {
    $initialCompanies = Company::factory()->count(3)->create();

    foreach ($initialCompanies as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    $remainingCompanies = $initialCompanies->take(2);
    $removedCompanies = $initialCompanies->skip(2);
    $newCompanies = Company::factory()->count(2)->create();
    $updatedCompanies = $remainingCompanies->merge($newCompanies);

    $changes = $this->detector->detectChanges($this->index, $updatedCompanies);

    expect($changes['additions'])->toHaveCount(2);
    expect($changes['additions']->pluck('id')->sort()->values()->toArray())
        ->toEqual($newCompanies->pluck('id')->sort()->values()->toArray());

    expect($changes['removals'])->toHaveCount(1);
    expect($changes['removals']->pluck('id')->toArray())
        ->toEqual($removedCompanies->pluck('id')->toArray());
});

it('correctly detects changes when comparing file against all historical holdings', function () {
    $companyA = Company::factory()->create(['isin' => 'US0000000001', 'name' => 'Company A']);
    $companyB = Company::factory()->create(['isin' => 'US0000000002', 'name' => 'Company B']);
    $companyC = Company::factory()->create(['isin' => 'US0000000003', 'name' => 'Company C']);

    foreach ([$companyA, $companyB, $companyC] as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    $companyD = Company::factory()->create(['isin' => 'US0000000004', 'name' => 'Company D']);

    $newFileCompanies = collect([$companyA, $companyB, $companyD]);
    $changes = $this->detector->detectChanges($this->index, $newFileCompanies);

    expect($changes['additions'])->toHaveCount(1);
    expect($changes['additions']->first()->id)->toBe($companyD->id);

    expect($changes['removals'])->toHaveCount(1);
    expect($changes['removals']->first()->id)->toBe($companyC->id);
});

it('does not report same removal multiple times after market data is updated', function () {
    $companyA = Company::factory()->create(['isin' => 'US0000000001']);
    $companyB = Company::factory()->create(['isin' => 'US0000000002']);
    $companyC = Company::factory()->create(['isin' => 'US0000000003']);

    foreach ([$companyA, $companyB, $companyC] as $company) {
        $holding = IndexHolding::withoutGlobalScopes()->create([
            'index_id' => $this->index->id,
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-01-01',
        ]);
    }

    expect(IndexHolding::where('index_id', $this->index->id)->count())->toBe(3);

    $changes1 = $this->detector->detectChanges($this->index, collect([$companyA, $companyB]));
    expect($changes1['removals'])->toHaveCount(1);
    expect($changes1['removals']->first()->id)->toBe($companyC->id);

    foreach ([$companyA, $companyB] as $company) {
        $holding = IndexHolding::withoutGlobalScopes()
            ->where('index_id', $this->index->id)
            ->where('company_id', $company->id)
            ->first();

        MarketData::factory()->create([
            'index_holding_id' => $holding->id,
            'date' => '2024-02-01',
        ]);
    }

    expect(IndexHolding::where('index_id', $this->index->id)->count())->toBe(2);

    $changes2 = $this->detector->detectChanges($this->index, collect([$companyA, $companyB]));
    expect($changes2['removals'])->toHaveCount(0);
    expect($changes2['additions'])->toHaveCount(0);
});
