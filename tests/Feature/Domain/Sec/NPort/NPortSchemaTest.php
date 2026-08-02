<?php

use App\Domain\Sec\Imports\Enums\DatasetType;
use App\Domain\Sec\Imports\Enums\ImportRunStatus;
use App\Domain\Sec\Imports\Models\DataQualityIssue;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\Imports\Models\ImportRun;
use App\Domain\Sec\NPort\Data\NPortQuarter;
use App\Domain\Sec\NPort\Models\Filing;
use App\Domain\Sec\NPort\Models\Fund;
use App\Domain\Sec\NPort\Models\FundReport;
use App\Domain\Sec\NPort\Models\Registrant;
use Illuminate\Database\QueryException;

it('stores release versions and import attempts', function () {
    $release = DatasetRelease::factory()->create([
        'dataset' => DatasetType::NPort,
        'year' => 2026,
        'quarter' => 1,
        'sha256' => str_repeat('a', 64),
        'version' => 1,
    ]);

    $run = ImportRun::factory()->for($release, 'datasetRelease')->create();

    expect($release->importRuns)->toHaveCount(1)
        ->and($run->status)->toBe(ImportRunStatus::Running)
        ->and($release->dataset)->toBe(DatasetType::NPort)
        ->and($release->retrieved_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('derives default release dates from the N-PORT source contract', function () {
    $release = DatasetRelease::factory()->make();
    $firstPublicQuarter = NPortQuarter::firstPublic();

    expect($release->year)->toBe($firstPublicQuarter->year)
        ->and($release->quarter)->toBe($firstPublicQuarter->quarter)
        ->and($release->source_url)->toContain($firstPublicQuarter->slug());
});

it('protects each release identity constraint', function (array $duplicateAttributes) {
    DatasetRelease::factory()->create([
        'dataset' => DatasetType::NPort,
        'year' => 2026,
        'quarter' => 1,
        'version' => 1,
        'archive_path' => 'sec/nport/2026/q1/original.zip',
        'sha256' => str_repeat('a', 64),
    ]);

    expect(fn () => DatasetRelease::factory()->create($duplicateAttributes))
        ->toThrow(QueryException::class);
})->with([
    'archive path' => [[
        'year' => 2026,
        'quarter' => 2,
        'version' => 1,
        'archive_path' => 'sec/nport/2026/q1/original.zip',
        'sha256' => str_repeat('b', 64),
    ]],
    'period and checksum' => [[
        'year' => 2026,
        'quarter' => 1,
        'version' => 2,
        'archive_path' => 'sec/nport/2026/q1/checksum-duplicate.zip',
        'sha256' => str_repeat('a', 64),
    ]],
    'period and version' => [[
        'year' => 2026,
        'quarter' => 1,
        'version' => 1,
        'archive_path' => 'sec/nport/2026/q1/version-duplicate.zip',
        'sha256' => str_repeat('b', 64),
    ]],
]);

it('records quality issues against a release and import run', function () {
    $run = ImportRun::factory()->create();

    $issue = DataQualityIssue::factory()
        ->for($run)
        ->for($run->datasetRelease)
        ->create([
            'source_key' => '000000000',
            'raw_data' => ['CUSIP' => '000000000'],
            'prevents_publication' => false,
        ]);

    expect($issue->importRun->is($run))->toBeTrue()
        ->and($issue->datasetRelease->is($run->datasetRelease))->toBeTrue()
        ->and($issue->raw_data)->toBe(['CUSIP' => '000000000'])
        ->and($issue->prevents_publication)->toBeFalse()
        ->and($run->dataQualityIssues)->toHaveCount(1);
});

it('completes an import run', function () {
    $run = ImportRun::factory()->create();

    $run->complete(['published' => 42], 1250);

    expect($run->refresh()->status)->toBe(ImportRunStatus::Completed)
        ->and($run->counts)->toBe(['published' => 42])
        ->and($run->duration_ms)->toBe(1250)
        ->and($run->warning_count)->toBe(0)
        ->and($run->finished_at)->toBeInstanceOf(DateTimeInterface::class)
        ->and($run->failure_message)->toBeNull();
});

it('completes an import run with warnings', function () {
    $run = ImportRun::factory()->create();

    $run->completeWithWarnings(['published' => 41, 'quarantined' => 1], 1300, 1);

    expect($run->refresh()->status)->toBe(ImportRunStatus::CompletedWithWarnings)
        ->and($run->counts)->toBe(['published' => 41, 'quarantined' => 1])
        ->and($run->duration_ms)->toBe(1300)
        ->and($run->warning_count)->toBe(1)
        ->and($run->finished_at)->toBeInstanceOf(DateTimeInterface::class);
});

it('records a failed import without obscuring its cause', function () {
    $run = ImportRun::factory()->create();

    $run->fail(new RuntimeException('The archive could not be opened.'));

    expect($run->refresh()->status)->toBe(ImportRunStatus::Failed)
        ->and($run->failure_message)->toBe('The archive could not be opened.')
        ->and($run->finished_at)->toBeInstanceOf(DateTimeInterface::class);
});

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
