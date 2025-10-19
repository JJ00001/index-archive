<?php

use App\Models\Company;
use App\Models\Index;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('index_holdings', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Index::class)->constrained();
            $table->foreignIdFor(Company::class)->constrained();
            $table->timestamps();

            $table->unique(['index_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('index_holdings');
    }
};
