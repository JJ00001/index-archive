<?php

use App\Http\Services\HoldingImport\IndexHoldingService;
use App\Models\Company;
use App\Models\Index;
use App\Models\IndexHolding;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->index   = Index::factory()->create();
    $this->service = app(IndexHoldingService::class);
});

it('keeps existing holdings active when company remains in the import', function () {
    $company = Company::factory()->create(['isin' => 'US0000000001']);

    createHolding($this->index, $company, true);

    $this->service->createForIndex($this->index, collect([$company]));

    $isActive = IndexHolding::withoutGlobalScopes()
                            ->where('index_id', $this->index->id)
                            ->where('company_id', $company->id)
                            ->value('is_active');

    expect($isActive)->toBeTrue();
});

it('deactivates holdings for companies missing from the import', function () {
    $companyPersisting   = Company::factory()->create(['isin' => 'US0000000010']);
    $companyToDeactivate = Company::factory()->create(['isin' => 'US0000000011']);

    createHolding($this->index, $companyPersisting, true);
    createHolding($this->index, $companyToDeactivate, true);

    $this->service->createForIndex($this->index, collect([$companyPersisting]));

    $isActive = IndexHolding::withoutGlobalScopes()
                            ->where('index_id', $this->index->id)
                            ->where('company_id', $companyToDeactivate->id)
                            ->value('is_active');

    expect($isActive)->toBeFalse();
});

it('reactivates previously inactive holdings when company returns in the import', function () {
    $company = Company::factory()->create(['isin' => 'US0000000021']);

    createHolding($this->index, $company, false);

    $this->service->createForIndex($this->index, collect([$company]));

    $isActive = IndexHolding::withoutGlobalScopes()
                            ->where('index_id', $this->index->id)
                            ->where('company_id', $company->id)
                            ->value('is_active');

    expect($isActive)->toBeTrue();
});

it('creates new holdings as active for companies not previously linked to the index', function () {
    $existingCompany = Company::factory()->create(['isin' => 'US0000000031']);
    $newCompany      = Company::factory()->create(['isin' => 'US0000000032']);

    createHolding($this->index, $existingCompany, true);

    $this->service->createForIndex($this->index, collect([$existingCompany, $newCompany]));

    $newHolding = IndexHolding::withoutGlobalScopes()
                              ->where('index_id', $this->index->id)
                              ->where('company_id', $newCompany->id)
                              ->first();

    expect($newHolding)->not->toBeNull();
    expect($newHolding->is_active)->toBeTrue();
});

function createHolding(Index $index, Company $company, bool $isActive): IndexHolding
{
    return IndexHolding::withoutGlobalScopes()->create([
        'index_id' => $index->id,
        'company_id' => $company->id,
        'is_active' => $isActive,
    ]);
}
