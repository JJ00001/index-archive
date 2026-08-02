<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sec_data_quality_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sec_import_run_id')
                ->nullable()
                ->constrained('sec_import_runs')
                ->nullOnDelete();
            $table->foreignId('sec_dataset_release_id')
                ->nullable()
                ->constrained('sec_dataset_releases')
                ->nullOnDelete();
            $table->string('source_file');
            $table->unsignedBigInteger('source_line')->nullable();
            $table->text('source_key')->nullable();
            $table->string('reason_code');
            $table->text('message');
            $table->boolean('prevents_publication')->default(false);
            $table->jsonb('raw_data')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sec_data_quality_issues');
    }
};
