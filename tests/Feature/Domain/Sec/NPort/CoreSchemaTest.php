<?php

use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\NPort\Models\Filing;
use App\Domain\Sec\NPort\Models\Fund;
use App\Domain\Sec\NPort\Models\FundReport;
use App\Domain\Sec\NPort\Models\Registrant;
use Illuminate\Database\QueryException;

it('stores the as-filed hierarchy independently from canonical fund identity', function () {
    $registrant = Registrant::factory()->create(['cik' => '0000123456']);
    $fund = Fund::factory()->for($registrant)->create(['series_id' => 'S000000001']);
    $filing = Filing::factory()->for($registrant)->create([
        'accession_number' => '0000000000-26-000001',
        'report_date' => '2026-03-31',
        'registrant_snapshot' => ['name' => 'Registrant as filed'],
    ]);
    $report = FundReport::factory()->for($filing)->for($fund)->create();

    expect($report->filing->is($filing))->toBeTrue()
        ->and($report->fund?->is($fund))->toBeTrue()
        ->and($filing->registrant?->is($registrant))->toBeTrue()
        ->and($filing->firstSeenDatasetRelease)->toBeInstanceOf(DatasetRelease::class)
        ->and($filing->registrant_snapshot)->toBe(['name' => 'Registrant as filed'])
        ->and($fund->registrant?->is($registrant))->toBeTrue()
        ->and($registrant->filings)->toHaveCount(1)
        ->and($registrant->funds)->toHaveCount(1);
});

it('keeps a complete as-filed report when its series id is blank', function () {
    $report = FundReport::factory()
        ->withoutFund()
        ->for(Filing::factory()->state(['registrant_id' => null]))
        ->create([
            'reported_series_id' => null,
            'series_name' => 'Non-series registrant portfolio',
            'total_assets' => '100.000000000000',
        ]);

    expect($report->fund)->toBeNull()
        ->and($report->reported_series_id)->toBeNull()
        ->and($report->filing)->toBeInstanceOf(Filing::class)
        ->and($report->filing->registrant)->toBeNull()
        ->and($report->series_name)->toBe('Non-series registrant portfolio')
        ->and($report->total_assets)->toBe('100.000000000000');
});

it('allows a canonical fund to remain unresolved from a registrant', function () {
    $fund = Fund::factory()->create(['registrant_id' => null]);

    expect($fund->registrant)->toBeNull();
});

it('keeps unresolved amendments without guessing their original filing', function () {
    $amendment = Filing::factory()->create([
        'submission_type' => 'NPORT-P/A',
        'supersedes_filing_id' => null,
        'is_effective' => true,
    ]);

    expect($amendment->supersedes)->toBeNull()
        ->and($amendment->submission_type)->toBe('NPORT-P/A')
        ->and($amendment->is_effective)->toBeTrue();
});

it('links a deterministic filing correction without removing either filing', function () {
    $original = Filing::factory()->create(['is_effective' => false]);
    $amendment = Filing::factory()->create([
        'submission_type' => 'NPORT-P/A',
        'supersedes_filing_id' => $original->id,
    ]);

    expect($amendment->supersedes?->is($original))->toBeTrue()
        ->and($original->corrections->first()?->is($amendment))->toBeTrue();
});

it('preserves exact large fund report decimals as strings', function () {
    $report = FundReport::factory()->create([
        'total_assets' => '123456789012345678901234.123456789012',
        'net_assets' => '-.040000000000',
    ]);

    expect($report->refresh()->total_assets)
        ->toBe('123456789012345678901234.123456789012')
        ->and($report->net_assets)->toBe('-0.040000000000');
});

it('enforces canonical and as-filed uniqueness', function (string $model, array $attributes) {
    $model::factory()->create($attributes);

    expect(fn () => $model::factory()->create($attributes))
        ->toThrow(QueryException::class);
})->with([
    'registrant CIK' => [Registrant::class, ['cik' => '0000123456']],
    'fund series id' => [Fund::class, ['series_id' => 'S000000001']],
    'filing accession number' => [Filing::class, ['accession_number' => '0000000000-26-000001']],
]);

it('allows only one fund report per filing', function () {
    $filing = Filing::factory()->create();

    FundReport::factory()->for($filing)->create();

    expect(fn () => FundReport::factory()->for($filing)->create())
        ->toThrow(QueryException::class);
});

it('protects filing correction history from deletion', function () {
    $original = Filing::factory()->create();
    Filing::factory()->create(['supersedes_filing_id' => $original->id]);

    expect(fn () => $original->delete())->toThrow(QueryException::class);
});

it('protects a filing that owns an as-filed report from deletion', function () {
    $filing = Filing::factory()->create();
    FundReport::factory()->for($filing)->create();

    expect(fn () => $filing->delete())->toThrow(QueryException::class);
});
