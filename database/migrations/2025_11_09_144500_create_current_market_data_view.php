<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_market_data');

        DB::statement(<<<'SQL'
            CREATE VIEW current_market_data AS
            SELECT
                md.*
            FROM market_data md
            INNER JOIN index_holdings ih ON ih.id = md.index_holding_id
            INNER JOIN index_latest_market_data_dates latest_dates ON latest_dates.index_id = ih.index_id
                AND latest_dates.latest_date = md.date
        SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_market_data');
    }

};
