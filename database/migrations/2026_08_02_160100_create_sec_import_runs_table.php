<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sec_import_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sec_dataset_release_id')
                ->constrained('sec_dataset_releases')
                ->cascadeOnDelete();
            $table->string('status');
            $table->timestampTz('started_at');
            $table->timestampTz('finished_at')->nullable();
            $table->unsignedBigInteger('duration_ms')->nullable();
            $table->jsonb('counts')->default('{}');
            $table->unsignedBigInteger('warning_count')->default(0);
            $table->text('failure_message')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sec_import_runs');
    }
};
