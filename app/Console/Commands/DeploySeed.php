<?php

namespace App\Console\Commands;

use Database\Seeders\AnnouncedLgaResultsTableSeeder;
use Database\Seeders\AnnouncedPuResultsTableSeeder;
use Database\Seeders\LgaTableSeeder;
use Database\Seeders\PollingUnitTableSeeder;
use Database\Seeders\StatesTableSeeder;
use Database\Seeders\WardTableSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeploySeed extends Command
{
    /**
     * Seeder and primary key column for each table. Order matters: children
     * follow their parents.
     */
    private const TABLES = [
        'states' => [StatesTableSeeder::class, 'state_id'],
        'lga' => [LgaTableSeeder::class, 'uniqueid'],
        'ward' => [WardTableSeeder::class, 'uniqueid'],
        'polling_unit' => [PollingUnitTableSeeder::class, 'uniqueid'],
        'announced_pu_results' => [AnnouncedPuResultsTableSeeder::class, 'result_id'],
        'announced_lga_results' => [AnnouncedLgaResultsTableSeeder::class, 'result_id'],
    ];

    protected $signature = 'deploy:seed';

    protected $description = 'Seed each reference table that is still empty, then realign Postgres id sequences';

    public function handle(): int
    {
        foreach (self::TABLES as $table => [$seeder, $key]) {
            $count = DB::table($table)->count();

            if ($count > 0) {
                $this->components->twoColumnDetail($table, "$count rows, skipping");
            } else {
                $this->components->twoColumnDetail($table, '<fg=yellow>empty, seeding</>');

                $this->call('db:seed', ['--class' => $seeder, '--force' => true]);

                $this->components->twoColumnDetail($table, '<fg=green>'.DB::table($table)->count().' rows</>');
            }

            $this->realignSequence($table, $key);
        }

        return self::SUCCESS;
    }

    /**
     * The seeders insert explicit ids, which leaves a Postgres serial sequence
     * still pointing at 1. The next insert from the app would then collide with
     * a seeded row, so push the sequence past the highest id in the table.
     */
    private function realignSequence(string $table, string $key): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        $sequence = DB::scalar('select pg_get_serial_sequence(?, ?)', [$table, $key]);

        if ($sequence === null) {
            return; // Not an auto-incrementing column, so nothing to realign.
        }

        $next = ((int) DB::table($table)->max($key)) + 1;

        DB::statement("select setval('$sequence', $next, false)");

        $this->components->twoColumnDetail("  $table.$key sequence", "next id $next");
    }
}
