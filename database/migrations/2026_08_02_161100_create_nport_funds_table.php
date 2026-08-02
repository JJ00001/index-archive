<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nport_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrant_id')
                ->nullable()
                ->constrained('nport_registrants')
                ->nullOnDelete();
            $table->string('series_id', 10)->unique();
            $table->string('name')->nullable();
            $table->string('series_lei', 20)->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nport_funds');
    }
};
