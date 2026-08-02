<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sec_dataset_releases', function (Blueprint $table) {
            $table->id();
            $table->string('dataset');
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter');
            $table->unsignedInteger('version');
            $table->text('source_url');
            $table->string('archive_disk');
            $table->string('archive_path')->unique();
            $table->unsignedBigInteger('byte_size');
            $table->char('sha256', 64);
            $table->timestampTz('retrieved_at');
            $table->timestampsTz();

            $table->unique(['dataset', 'year', 'quarter', 'sha256']);
            $table->unique(['dataset', 'year', 'quarter', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sec_dataset_releases');
    }
};
