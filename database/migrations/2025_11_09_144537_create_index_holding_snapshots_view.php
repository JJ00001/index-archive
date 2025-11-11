<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_index_holding_market_data');

        DB::statement(<<<'SQL'
            CREATE VIEW current_index_holding_market_data AS
            SELECT
                ih.id AS index_holding_id,
                ih.index_id,
                ih.company_id,
                ih.is_active,
                cmd.date,
                cmd.weight
            FROM index_holdings ih
            LEFT JOIN current_market_data cmd ON cmd.index_holding_id = ih.id
            WHERE ih.is_active = TRUE
        SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS current_index_holding_market_data');
    }

};
