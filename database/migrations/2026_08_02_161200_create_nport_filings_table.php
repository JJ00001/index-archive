<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nport_filings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrant_id')
                ->nullable()
                ->constrained('nport_registrants')
                ->nullOnDelete();
            $table->foreignId('first_seen_dataset_release_id')
                ->constrained('sec_dataset_releases')
                ->restrictOnDelete();
            $table->string('accession_number', 20)->unique();
            $table->date('filing_date');
            $table->string('file_number')->nullable();
            $table->string('submission_type', 20);
            $table->date('report_ending_period');
            $table->date('report_date');
            $table->boolean('is_last_filing')->nullable();
            $table->foreignId('supersedes_filing_id')
                ->nullable()
                ->constrained('nport_filings')
                ->restrictOnDelete();
            $table->boolean('is_effective')->default(true);
            $table->jsonb('registrant_snapshot')->nullable();
            $table->timestampsTz();

            $table->index(['report_date', 'is_effective']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nport_filings');
    }
};
