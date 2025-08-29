<?php

use App\Models\Index;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            $table->foreignIdFor(Index::class)->nullable()->constrained(
            ); // TODO - change to not nullable after completing transition period
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(Index::class);
        });
    }

};
