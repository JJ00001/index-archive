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
        Schema::table('index_holdings', function (Blueprint $table) {
            $table->index('index_id', 'idx_index_holdings_index_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('index_holdings', function (Blueprint $table) {
            $table->dropIndex('idx_index_holdings_index_id');
        });
    }

};
