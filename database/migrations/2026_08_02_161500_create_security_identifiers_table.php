<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_identifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_id')
                ->constrained('securities')
                ->restrictOnDelete();
            $table->string('scheme', 20);
            $table->string('value', 150);
            $table->timestampsTz();

            $table->unique(['scheme', 'value']);
            $table->index('security_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_identifiers');
    }
};
