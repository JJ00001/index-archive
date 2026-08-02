<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nport_fund_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filing_id')
                ->unique()
                ->constrained('nport_filings')
                ->restrictOnDelete();
            $table->foreignId('fund_id')
                ->nullable()
                ->constrained('nport_funds')
                ->nullOnDelete();
            $table->string('series_name')->nullable();
            $table->string('reported_series_id', 10)->nullable();
            $table->string('series_lei', 20)->nullable();

            $numericColumns = [
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

            foreach ($numericColumns as $column) {
                $table->decimal($column, total: 36, places: 12)->nullable();
            }

            $table->boolean('is_non_cash_collateral')->nullable();
            $table->timestampsTz();

            $table->index('fund_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nport_fund_reports');
    }
};
