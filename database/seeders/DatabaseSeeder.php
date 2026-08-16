<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            StatesTableSeeder::class,
            LgaTableSeeder::class,
            WardTableSeeder::class,
            PollingUnitTableSeeder::class,
            AnnouncedPuResultsTableSeeder::class,
            AnnouncedLgaResultsTableSeeder::class,
        ]);
    }
}