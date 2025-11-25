<?php

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
            $table->index(['index_holding_id', 'date'], 'idx_market_data_index_holding_id_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            $table->dropForeign(['index_holding_id']);
            $table->dropIndex('idx_market_data_index_holding_id_date');
            $table->foreign('index_holding_id')
                  ->references('id')
                  ->on('index_holdings');
        });
    }

};
