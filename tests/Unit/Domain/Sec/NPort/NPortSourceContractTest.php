<?php

use App\Domain\Sec\NPort\Data\NPortFile;
use App\Domain\Sec\NPort\Data\NPortQuarter;

it('validates supported quarters and creates quarter slugs', function () {
    expect(NPortQuarter::firstPublic())->toEqual(NPortQuarter::from(2019, 4))
        ->and(NPortQuarter::firstPublic()->slug())->toBe('2019q4')
        ->and(NPortQuarter::from(2026, 2)->slug())->toBe('2026q2')
        ->and(fn () => NPortQuarter::from(2019, 3))
        ->toThrow(InvalidArgumentException::class, 'N-PORT datasets begin at 2019 Q4.')
        ->and(fn () => NPortQuarter::from(2026, 0))
        ->toThrow(InvalidArgumentException::class, 'N-PORT datasets begin at 2019 Q4.')
        ->and(fn () => NPortQuarter::from(2026, 5))
        ->toThrow(InvalidArgumentException::class, 'N-PORT datasets begin at 2019 Q4.');
});

it('defines the five selected source files and staging tables', function () {
    expect(array_map(
        fn (NPortFile $file): string => $file->filename(),
        NPortFile::cases(),
    ))->toBe([
        'SUBMISSION.tsv',
        'REGISTRANT.tsv',
        'FUND_REPORTED_INFO.tsv',
        'FUND_REPORTED_HOLDING.tsv',
        'IDENTIFIERS.tsv',
    ])->and(array_map(
        fn (NPortFile $file): string => $file->stagingTable(),
        NPortFile::cases(),
    ))->toBe([
        'nport_submission_staging',
        'nport_registrant_staging',
        'nport_fund_reported_info_staging',
        'nport_fund_reported_holding_staging',
        'nport_identifiers_staging',
    ]);
});

it('defines the verified exact source headers', function () {
    expect(NPortFile::Submission->headers())->toBe([
        'ACCESSION_NUMBER', 'FILING_DATE', 'FILE_NUM', 'SUB_TYPE',
        'REPORT_ENDING_PERIOD', 'REPORT_DATE', 'IS_LAST_FILING',
    ])->and(NPortFile::Registrant->headers())->toBe([
        'ACCESSION_NUMBER', 'CIK', 'REGISTRANT_NAME', 'FILE_NUM', 'LEI',
        'ADDRESS1', 'ADDRESS2', 'CITY', 'STATE', 'COUNTRY', 'ZIP', 'PHONE',
    ])->and(NPortFile::FundReportedInfo->headers())->toBe([
        'ACCESSION_NUMBER', 'SERIES_NAME', 'SERIES_ID', 'SERIES_LEI',
        'TOTAL_ASSETS', 'TOTAL_LIABILITIES', 'NET_ASSETS',
        'ASSETS_ATTRBT_TO_MISC_SECURITY', 'ASSETS_INVESTED',
        'BORROWING_PAY_WITHIN_1YR', 'CTRLD_COMPANIES_PAY_WITHIN_1YR',
        'OTHER_AFFILIA_PAY_WITHIN_1YR', 'OTHER_PAY_WITHIN_1YR',
        'BORROWING_PAY_AFTER_1YR', 'CTRLD_COMPANIES_PAY_AFTER_1YR',
        'OTHER_AFFILIA_PAY_AFTER_1YR', 'OTHER_PAY_AFTER_1YR',
        'DELAYED_DELIVERY', 'STANDBY_COMMITMENT', 'LIQUIDATION_PREFERENCE',
        'CASH_NOT_RPTD_IN_C_OR_D', 'CREDIT_SPREAD_3MON_INVEST',
        'CREDIT_SPREAD_1YR_INVEST', 'CREDIT_SPREAD_5YR_INVEST',
        'CREDIT_SPREAD_10YR_INVEST', 'CREDIT_SPREAD_30YR_INVEST',
        'CREDIT_SPREAD_3MON_NONINVEST', 'CREDIT_SPREAD_1YR_NONINVEST',
        'CREDIT_SPREAD_5YR_NONINVEST', 'CREDIT_SPREAD_10YR_NONINVEST',
        'CREDIT_SPREAD_30YR_NONINVEST', 'IS_NON_CASH_COLLATERAL',
        'NET_REALIZE_GAIN_NONDERIV_MON1', 'NET_UNREALIZE_AP_NONDERIV_MON1',
        'NET_REALIZE_GAIN_NONDERIV_MON2', 'NET_UNREALIZE_AP_NONDERIV_MON2',
        'NET_REALIZE_GAIN_NONDERIV_MON3', 'NET_UNREALIZE_AP_NONDERIV_MON3',
        'SALES_FLOW_MON1', 'REINVESTMENT_FLOW_MON1', 'REDEMPTION_FLOW_MON1',
        'SALES_FLOW_MON2', 'REINVESTMENT_FLOW_MON2', 'REDEMPTION_FLOW_MON2',
        'SALES_FLOW_MON3', 'REINVESTMENT_FLOW_MON3', 'REDEMPTION_FLOW_MON3',
    ])->and(NPortFile::FundReportedHolding->headers())->toBe([
        'ACCESSION_NUMBER', 'HOLDING_ID', 'ISSUER_NAME', 'ISSUER_LEI',
        'ISSUER_TITLE', 'ISSUER_CUSIP', 'BALANCE', 'UNIT', 'OTHER_UNIT_DESC',
        'CURRENCY_CODE', 'CURRENCY_VALUE', 'EXCHANGE_RATE', 'PERCENTAGE',
        'PAYOFF_PROFILE', 'ASSET_CAT', 'OTHER_ASSET', 'ISSUER_TYPE',
        'OTHER_ISSUER', 'INVESTMENT_COUNTRY', 'IS_RESTRICTED_SECURITY',
        'FAIR_VALUE_LEVEL', 'DERIVATIVE_CAT',
    ])->and(NPortFile::Identifiers->headers())->toBe([
        'HOLDING_ID', 'IDENTIFIERS_ID', 'IDENTIFIER_ISIN',
        'IDENTIFIER_TICKER', 'OTHER_IDENTIFIER', 'OTHER_IDENTIFIER_DESC',
    ]);
});

it('defines the documented source keys', function () {
    expect(NPortFile::Submission->sourceKeyHeaders())->toBe(['ACCESSION_NUMBER'])
        ->and(NPortFile::Registrant->sourceKeyHeaders())->toBe(['ACCESSION_NUMBER'])
        ->and(NPortFile::FundReportedInfo->sourceKeyHeaders())->toBe(['ACCESSION_NUMBER'])
        ->and(NPortFile::FundReportedHolding->sourceKeyHeaders())
        ->toBe(['ACCESSION_NUMBER', 'HOLDING_ID'])
        ->and(NPortFile::Identifiers->sourceKeyHeaders())
        ->toBe(['HOLDING_ID', 'IDENTIFIERS_ID']);
});
