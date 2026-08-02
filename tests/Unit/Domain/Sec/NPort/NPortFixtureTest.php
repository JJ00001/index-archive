<?php

use App\Domain\Sec\NPort\Data\NPortFile;
use Tests\Support\Sec\NPort\NPortFixtureArchive;
use Tests\Support\Sec\NPort\NPortFixtureReader;

dataset('nport fixture files', NPortFile::cases());

dataset('nport fixture relationships', [
    'registrants reference submissions' => [
        NPortFile::Registrant, 'ACCESSION_NUMBER', NPortFile::Submission, 'ACCESSION_NUMBER',
    ],
    'fund reports reference submissions' => [
        NPortFile::FundReportedInfo, 'ACCESSION_NUMBER', NPortFile::Submission, 'ACCESSION_NUMBER',
    ],
    'holdings reference submissions' => [
        NPortFile::FundReportedHolding, 'ACCESSION_NUMBER', NPortFile::Submission, 'ACCESSION_NUMBER',
    ],
    'identifiers reference holdings' => [
        NPortFile::Identifiers, 'HOLDING_ID', NPortFile::FundReportedHolding, 'HOLDING_ID',
    ],
]);

it('matches the exact source header and row width', function (NPortFile $file) {
    $fixture = NPortFixtureReader::valid($file);

    expect($fixture->headers())->toBe($file->headers())
        ->and($fixture->rowWidths())->each->toBe(count($file->headers()));
})->with('nport fixture files');

it('includes an amendment of an original submission', function () {
    $submissions = NPortFixtureReader::valid(NPortFile::Submission);
    $original = $submissions->firstWhere('ACCESSION_NUMBER', '0000000000-26-000001');
    $amendment = $submissions->firstWhere('SUB_TYPE', 'NPORT-P/A');

    expect($original['SUB_TYPE'])->toBe('NPORT-P')
        ->and($amendment['REPORT_DATE'])->toBe($original['REPORT_DATE']);
});

it('includes a fund report without a series identifier', function () {
    $reports = NPortFixtureReader::valid(NPortFile::FundReportedInfo);

    expect($reports->where('SERIES_ID', ''))->toHaveCount(1);
});

it('includes valid decimals without leading zeroes', function () {
    $holdings = NPortFixtureReader::valid(NPortFile::FundReportedHolding);
    $firstHolding = $holdings->firstWhere('HOLDING_ID', 'HOLDING-000001');

    expect($firstHolding['BALANCE'])->toBe('.96')
        ->and($firstHolding['PERCENTAGE'])->toBe('-.04');
});

it('includes a repeated identifier across different holdings', function () {
    $identifiers = NPortFixtureReader::valid(NPortFile::Identifiers);
    $appleIdentifiers = $identifiers->where('IDENTIFIER_ISIN', 'US0378331005');

    expect($appleIdentifiers)->toHaveCount(2)
        ->and(array_column($appleIdentifiers, 'HOLDING_ID'))
        ->toBe(['HOLDING-000001', 'HOLDING-000002']);
});

it('includes identifier sentinel values without treating them as missing', function () {
    $identifiers = NPortFixtureReader::valid(NPortFile::Identifiers);
    $holdings = NPortFixtureReader::valid(NPortFile::FundReportedHolding);

    expect($identifiers->where('IDENTIFIER_ISIN', 'N/A'))->toHaveCount(1)
        ->and($holdings->where('ISSUER_CUSIP', '000000000'))->toHaveCount(1);
});

it('includes an unresolved holding with no identifier record', function () {
    $holdings = NPortFixtureReader::valid(NPortFile::FundReportedHolding);
    $identifiers = NPortFixtureReader::valid(NPortFile::Identifiers);
    $unresolvedHolding = $holdings->firstWhere('HOLDING_ID', 'HOLDING-000004');

    expect($unresolvedHolding['ISSUER_CUSIP'])->toBe('')
        ->and($unresolvedHolding['EXCHANGE_RATE'])->toBe('not-a-number')
        ->and($identifiers->where('HOLDING_ID', 'HOLDING-000004'))->toBe([]);
});

it('includes a deliberately sparse swap holding', function () {
    $holdings = NPortFixtureReader::valid(NPortFile::FundReportedHolding);
    $sparseSwap = $holdings->firstWhere('DERIVATIVE_CAT', 'SWP');
    $populatedFields = array_filter(
        $sparseSwap,
        fn (string $value): bool => $value !== '',
    );

    expect(array_keys($populatedFields))->toBe([
        'ACCESSION_NUMBER',
        'HOLDING_ID',
        'DERIVATIVE_CAT',
    ]);
});

it('includes a duplicate holding source key', function () {
    $holdings = NPortFixtureReader::valid(NPortFile::FundReportedHolding);
    $duplicateHolding = $holdings->where('HOLDING_ID', 'HOLDING-000001');

    expect($duplicateHolding)->toHaveCount(2)
        ->and(array_column($duplicateHolding, 'ACCESSION_NUMBER'))
        ->each->toBe('0000000000-26-000001');
});

it('keeps cross-file references internally consistent', function (
    NPortFile $sourceFile,
    string $sourceKey,
    NPortFile $targetFile,
    string $targetKey,
) {
    $sourceValues = array_column(NPortFixtureReader::valid($sourceFile)->records(), $sourceKey);
    $targetValues = array_column(NPortFixtureReader::valid($targetFile)->records(), $targetKey);

    expect(array_values(array_diff($sourceValues, $targetValues)))->toBe([]);
})->with('nport fixture relationships');

it('contains exactly one malformed row with too few columns', function () {
    $wrongWidthRows = array_merge(...array_map(
        fn (NPortFile $file): array => NPortFixtureReader::malformed($file)->wrongWidthRows(),
        NPortFile::cases(),
    ));

    expect($wrongWidthRows)->toBe([[
        '0000000000-26-000010',
        'HOLDING-MALFORMED',
        'Too',
        'Few',
        'Columns',
    ]]);
});

it('creates an archive containing the exact fixture files and bytes', function () {
    $fixturesDirectory = NPortFixtureReader::fixturesDirectory().'/valid';
    $destination = tempnam(sys_get_temp_dir(), 'nport-fixture-');

    if ($destination === false) {
        throw new RuntimeException('Unable to reserve a temporary archive path.');
    }

    try {
        expect(NPortFixtureArchive::create($fixturesDirectory, $destination))->toBe($destination);

        $zip = new ZipArchive;

        expect($zip->open($destination))->toBeTrue()
            ->and($zip->numFiles)->toBe(count(NPortFile::cases()));

        foreach (NPortFile::cases() as $index => $file) {
            $sourcePath = $fixturesDirectory.'/'.$file->filename();

            expect($zip->getNameIndex($index))->toBe($file->filename())
                ->and($zip->getFromName($file->filename()))
                ->toBe(file_get_contents($sourcePath))
                ->and($sourcePath)->toBeFile();
        }

        $zip->close();
    } finally {
        if (is_file($destination)) {
            unlink($destination);
        }
    }
});
