<?php

use App\Models\Company;
use App\Models\Country;
use App\Models\Index;
use App\Models\IndexHolding;
use App\Models\IndexProvider;
use App\Models\MarketData;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns sector weight history scoped to an index', function () {
    $index      = createTestIndex();
    $otherIndex = createTestIndex();

    $sector  = Sector::factory()->create();
    $country = Country::factory()->create();

    createIndexHoldingWithWeights($index, $sector, $country, [
        '2025-01-01' => 0.10,
        '2025-01-02' => 0.15,
    ]);

    createIndexHoldingWithWeights($otherIndex, $sector, $country, [
        '2025-01-01' => 0.90,
    ]);

    $response = $this->getJson(route('api.indices.sectors.show', [$index, $sector]));

    $response->assertOk();
    $response->assertJsonPath('props.sector.id', $sector->id);
    $response->assertJsonPath('props.sector.weight_history.labels', ['2025-01-01', '2025-01-02']);
    $response->assertJsonPath('props.sector.weight_history.datasets.weight', [0.10, 0.15]);
});

it('returns country weight history scoped to an index', function () {
    $index      = createTestIndex();
    $otherIndex = createTestIndex();

    $sector  = Sector::factory()->create();
    $country = Country::factory()->create();

    createIndexHoldingWithWeights($index, $sector, $country, [
        '2025-01-01' => 0.20,
        '2025-01-03' => 0.30,
    ]);

    createIndexHoldingWithWeights($otherIndex, $sector, $country, [
        '2025-01-01' => 0.40,
    ]);

    $response = $this->getJson(route('api.indices.countries.show', [$index, $country]));

    $response->assertOk();
    $response->assertJsonPath('props.country.id', $country->id);
    $response->assertJsonPath('props.country.weight_history.labels', ['2025-01-01', '2025-01-03']);
    $response->assertJsonPath('props.country.weight_history.datasets.weight', [0.20, 0.30]);
});

it('returns index holding weight history scoped to its index', function () {
    $index   = createTestIndex();
    $sector  = Sector::factory()->create();
    $country = Country::factory()->create();

    $indexHolding = createIndexHoldingWithWeights($index, $sector, $country, [
        '2025-02-01' => 0.25,
        '2025-02-02' => 0.35,
    ]);

    $response = $this->getJson(route('api.index-holdings.show', [$indexHolding]));

    $response->assertOk();
    $response->assertJsonPath('props.indexHolding.id', $indexHolding->id);
    $response->assertJsonPath('props.indexHolding.weight_history.labels', ['2025-02-01', '2025-02-02']);
    $response->assertJsonPath('props.indexHolding.weight_history.datasets.weight', [0.25, 0.35]);
});

function createTestIndex(): Index
{
    $indexProvider = IndexProvider::factory()->create();

    return Index::factory()->for($indexProvider)->create();
}

function createIndexHoldingWithWeights(
    Index $index,
    Sector $sector,
    Country $country,
    array $weightsByDate
): IndexHolding {
    $company = Company::factory()
                      ->for($sector)
                      ->for($country)
                      ->create();

    $indexHolding = IndexHolding::factory()->create([
        'is_active' => true,
        'index_id' => $index->id,
        'company_id' => $company->id,
    ]);

    foreach ($weightsByDate as $date => $weight) {
        MarketData::factory()
                  ->for($indexHolding)
                  ->create([
                      'date' => $date,
                      'weight' => $weight,
                  ]);
    }

    return $indexHolding;
}
