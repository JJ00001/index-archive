<?php

namespace App\Domain\Sec\NPort\Models;

use Database\Factories\FundReportFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(FundReportFactory::class)]
class FundReport extends Model
{
    use HasFactory;

    protected $table = 'nport_fund_reports';

    protected $fillable = [
        'filing_id',
        'fund_id',
        'series_name',
        'reported_series_id',
        'series_lei',
        'total_assets',
        'total_liabilities',
        'net_assets',
        'assets_attrbt_to_misc_security',
        'assets_invested',
        'borrowing_pay_within_1yr',
        'ctrld_companies_pay_within_1yr',
        'other_affilia_pay_within_1yr',
        'other_pay_within_1yr',
        'borrowing_pay_after_1yr',
        'ctrld_companies_pay_after_1yr',
        'other_affilia_pay_after_1yr',
        'other_pay_after_1yr',
        'delayed_delivery',
        'standby_commitment',
        'liquidation_preference',
        'cash_not_rptd_in_c_or_d',
        'credit_spread_3mon_invest',
        'credit_spread_1yr_invest',
        'credit_spread_5yr_invest',
        'credit_spread_10yr_invest',
        'credit_spread_30yr_invest',
        'credit_spread_3mon_noninvest',
        'credit_spread_1yr_noninvest',
        'credit_spread_5yr_noninvest',
        'credit_spread_10yr_noninvest',
        'credit_spread_30yr_noninvest',
        'is_non_cash_collateral',
        'net_realize_gain_nonderiv_mon1',
        'net_unrealize_ap_nonderiv_mon1',
        'net_realize_gain_nonderiv_mon2',
        'net_unrealize_ap_nonderiv_mon2',
        'net_realize_gain_nonderiv_mon3',
        'net_unrealize_ap_nonderiv_mon3',
        'sales_flow_mon1',
        'reinvestment_flow_mon1',
        'redemption_flow_mon1',
        'sales_flow_mon2',
        'reinvestment_flow_mon2',
        'redemption_flow_mon2',
        'sales_flow_mon3',
        'reinvestment_flow_mon3',
        'redemption_flow_mon3',
    ];

    /** @return BelongsTo<Filing, $this> */
    public function filing(): BelongsTo
    {
        return $this->belongsTo(Filing::class, 'filing_id');
    }

    /** @return BelongsTo<Fund, $this> */
    public function fund(): BelongsTo
    {
        return $this->belongsTo(Fund::class, 'fund_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_non_cash_collateral' => 'boolean',
        ];
    }
}
