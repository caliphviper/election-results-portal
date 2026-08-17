<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * announced_pu_results.polling_unit_uniqueid was varchar because the
     * original dump had it that way, but it holds polling_unit.uniqueid, which
     * is an integer. SQLite compares the two happily; Postgres rejects the join
     * outright, so the LGA summary query fails in production only.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return; // SQLite is dynamically typed, so the column needs no change.
        }

        DB::statement('alter table announced_pu_results alter column polling_unit_uniqueid type integer using polling_unit_uniqueid::integer');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('alter table announced_pu_results alter column polling_unit_uniqueid type varchar(50)');
    }
};
