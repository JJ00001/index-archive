<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nport_position_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')
                ->constrained('nport_positions')
                ->restrictOnDelete();
            $table->string('sec_identifiers_id', 38);
            $table->string('identifier_isin', 150)->nullable();
            $table->string('identifier_ticker', 150)->nullable();
            $table->string('other_identifier', 150)->nullable();
            $table->string('other_identifier_description')->nullable();
            $table->timestampsTz();

            $table->unique(['position_id', 'sec_identifiers_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nport_position_identifiers');
    }
};
