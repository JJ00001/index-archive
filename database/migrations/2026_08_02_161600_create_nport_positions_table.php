<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nport_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_report_id')
                ->constrained('nport_fund_reports')
                ->restrictOnDelete();
            $table->foreignId('security_id')
                ->nullable()
                ->constrained('securities')
                ->nullOnDelete();
            $table->string('sec_holding_id', 38);
            $table->string('issuer_name')->nullable();
            $table->string('issuer_lei', 20)->nullable();
            $table->string('issuer_title')->nullable();
            $table->string('issuer_cusip', 150)->nullable();
            $table->decimal('balance', total: 36, places: 12)->nullable();
            $table->string('unit', 20)->nullable();
            $table->string('other_unit_description')->nullable();
            $table->string('currency_code', 3)->nullable();
            $table->decimal('currency_value', total: 36, places: 12)->nullable();
            $table->decimal('exchange_rate', total: 36, places: 12)->nullable();
            $table->decimal('percentage', total: 36, places: 12)->nullable();
            $table->string('payoff_profile', 20)->nullable();
            $table->string('asset_category', 20)->nullable();
            $table->string('other_asset_description')->nullable();
            $table->string('issuer_type', 20)->nullable();
            $table->string('other_issuer_description')->nullable();
            $table->string('investment_country', 3)->nullable();
            $table->boolean('is_restricted_security')->nullable();
            $table->string('fair_value_level', 20)->nullable();
            $table->string('derivative_category', 20)->nullable();
            $table->timestampsTz();

            $table->unique(['fund_report_id', 'sec_holding_id']);
            $table->index('security_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nport_positions');
    }
};
