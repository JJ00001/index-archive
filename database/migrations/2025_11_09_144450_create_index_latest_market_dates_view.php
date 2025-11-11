<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS index_latest_market_data_dates');

        DB::statement(<<<'SQL'
            CREATE VIEW index_latest_market_data_dates AS
            SELECT
                ih.index_id,
                MAX(md.date) AS latest_date
            FROM market_data md
            INNER JOIN index_holdings ih ON ih.id = md.index_holding_id
            GROUP BY ih.index_id
        SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS index_latest_market_data_dates');
    }

};
