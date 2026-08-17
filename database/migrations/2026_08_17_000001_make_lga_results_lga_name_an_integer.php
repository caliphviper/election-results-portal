<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * announced_lga_results.lga_name is misleadingly named: it holds lga.lga_id,
     * as a string. The LGA summary comparison matches it against that integer
     * column, which Postgres will not do across types.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return; // SQLite is dynamically typed, so the column needs no change.
        }

        DB::statement('alter table announced_lga_results alter column lga_name type integer using lga_name::integer');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('alter table announced_lga_results alter column lga_name type varchar(50)');
    }
};
