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
     * Seeders keyed by the table they fill. Order matters: children follow
     * their parents.
     */
    private const SEEDERS = [
        'states' => StatesTableSeeder::class,
        'lga' => LgaTableSeeder::class,
        'ward' => WardTableSeeder::class,
        'polling_unit' => PollingUnitTableSeeder::class,
        'announced_pu_results' => AnnouncedPuResultsTableSeeder::class,
        'announced_lga_results' => AnnouncedLgaResultsTableSeeder::class,
    ];

    protected $signature = 'deploy:seed';

    protected $description = 'Seed each reference table that is still empty, leaving populated tables untouched';

    public function handle(): int
    {
        foreach (self::SEEDERS as $table => $seeder) {
            $count = DB::table($table)->count();

            if ($count > 0) {
                $this->components->twoColumnDetail($table, "$count rows, skipping");

                continue;
            }

            $this->components->twoColumnDetail($table, '<fg=yellow>empty, seeding</>');

            $this->call('db:seed', ['--class' => $seeder, '--force' => true]);

            $this->components->twoColumnDetail($table, '<fg=green>'.DB::table($table)->count().' rows</>');
        }

        return self::SUCCESS;
    }
}
