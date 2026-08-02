<?php

use App\Domain\Sec\Imports\Enums\DatasetType;
use App\Domain\Sec\Imports\Enums\ImportRunStatus;
use App\Domain\Sec\Imports\Models\DataQualityIssue;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\Imports\Models\ImportRun;
use App\Domain\Sec\NPort\Data\NPortQuarter;
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
