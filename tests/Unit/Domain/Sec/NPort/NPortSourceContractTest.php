<?php

use App\Domain\Sec\NPort\Data\NPortFile;
use App\Domain\Sec\NPort\Data\NPortQuarter;

dataset('nport source files', [
    'submissions' => [NPortFile::Submission, 'SUBMISSION.tsv', 'nport_submission_staging'],
    'registrants' => [NPortFile::Registrant, 'REGISTRANT.tsv', 'nport_registrant_staging'],
    'fund reports' => [NPortFile::FundReportedInfo, 'FUND_REPORTED_INFO.tsv', 'nport_fund_reported_info_staging'],
    'holdings' => [NPortFile::FundReportedHolding, 'FUND_REPORTED_HOLDING.tsv', 'nport_fund_reported_holding_staging'],
    'identifiers' => [NPortFile::Identifiers, 'IDENTIFIERS.tsv', 'nport_identifiers_staging'],
]);

dataset('nport source keys', [
    'submissions' => [NPortFile::Submission, ['ACCESSION_NUMBER']],
    'registrants' => [NPortFile::Registrant, ['ACCESSION_NUMBER']],
    'fund reports' => [NPortFile::FundReportedInfo, ['ACCESSION_NUMBER']],
    'holdings' => [NPortFile::FundReportedHolding, ['ACCESSION_NUMBER', 'HOLDING_ID']],
    'identifiers' => [NPortFile::Identifiers, ['HOLDING_ID', 'IDENTIFIERS_ID']],
]);

dataset('invalid nport quarters', [
    'before the first public release' => [2019, 3],
    'quarter below the valid range' => [2026, 0],
    'quarter above the valid range' => [2026, 5],
]);

it('identifies the first public quarter and creates quarter slugs', function () {
    expect(NPortQuarter::firstPublic())->toEqual(NPortQuarter::from(2019, 4))
        ->and(NPortQuarter::firstPublic()->slug())->toBe('2019q4')
        ->and(NPortQuarter::from(2026, 2)->slug())->toBe('2026q2');
});

it('rejects invalid quarters', function (int $year, int $quarter) {
    expect(fn () => NPortQuarter::from($year, $quarter))
        ->toThrow(InvalidArgumentException::class, 'N-PORT datasets begin at 2019 Q4.');
})->with('invalid nport quarters');

it('maps each selected source file to its staging table', function (
    NPortFile $file,
    string $filename,
    string $stagingTable,
) {
    expect($file->filename())->toBe($filename)
        ->and($file->stagingTable())->toBe($stagingTable);
})->with('nport source files');

it('defines the documented source key for each file', function (NPortFile $file, array $sourceKey) {
    expect($file->sourceKeyHeaders())->toBe($sourceKey);
})->with('nport source keys');
