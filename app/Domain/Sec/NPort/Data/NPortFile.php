<?php

namespace App\Domain\Sec\NPort\Data;

enum NPortFile: string
{
    case Submission = 'SUBMISSION.tsv';
    case Registrant = 'REGISTRANT.tsv';
    case FundReportedInfo = 'FUND_REPORTED_INFO.tsv';
    case FundReportedHolding = 'FUND_REPORTED_HOLDING.tsv';
    case Identifiers = 'IDENTIFIERS.tsv';

    public function filename(): string
    {
        return $this->value;
    }

    public function stagingTable(): string
    {
        return match ($this) {
            self::Submission => 'nport_submission_staging',
            self::Registrant => 'nport_registrant_staging',
            self::FundReportedInfo => 'nport_fund_reported_info_staging',
            self::FundReportedHolding => 'nport_fund_reported_holding_staging',
            self::Identifiers => 'nport_identifiers_staging',
        };
    }

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return match ($this) {
            self::Submission => [
                'ACCESSION_NUMBER', 'FILING_DATE', 'FILE_NUM', 'SUB_TYPE',
                'REPORT_ENDING_PERIOD', 'REPORT_DATE', 'IS_LAST_FILING',
            ],
            self::Registrant => [
                'ACCESSION_NUMBER', 'CIK', 'REGISTRANT_NAME', 'FILE_NUM', 'LEI',
                'ADDRESS1', 'ADDRESS2', 'CITY', 'STATE', 'COUNTRY', 'ZIP', 'PHONE',
            ],
            self::FundReportedInfo => [
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
            ],
            self::FundReportedHolding => [
                'ACCESSION_NUMBER', 'HOLDING_ID', 'ISSUER_NAME', 'ISSUER_LEI',
                'ISSUER_TITLE', 'ISSUER_CUSIP', 'BALANCE', 'UNIT', 'OTHER_UNIT_DESC',
                'CURRENCY_CODE', 'CURRENCY_VALUE', 'EXCHANGE_RATE', 'PERCENTAGE',
                'PAYOFF_PROFILE', 'ASSET_CAT', 'OTHER_ASSET', 'ISSUER_TYPE',
                'OTHER_ISSUER', 'INVESTMENT_COUNTRY', 'IS_RESTRICTED_SECURITY',
                'FAIR_VALUE_LEVEL', 'DERIVATIVE_CAT',
            ],
            self::Identifiers => [
                'HOLDING_ID', 'IDENTIFIERS_ID', 'IDENTIFIER_ISIN',
                'IDENTIFIER_TICKER', 'OTHER_IDENTIFIER', 'OTHER_IDENTIFIER_DESC',
            ],
        };
    }

    /**
     * @return list<string>
     */
    public function sourceKeyHeaders(): array
    {
        return match ($this) {
            self::Submission, self::Registrant, self::FundReportedInfo => ['ACCESSION_NUMBER'],
            self::FundReportedHolding => ['ACCESSION_NUMBER', 'HOLDING_ID'],
            self::Identifiers => ['HOLDING_ID', 'IDENTIFIERS_ID'],
        };
    }
}
