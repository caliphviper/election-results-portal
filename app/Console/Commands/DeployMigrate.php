<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeployMigrate extends Command
{
    /**
     * Postgres advisory lock id. Any container booting this app uses the same
     * id, so only one of them migrates at a time.
     */
    private const LOCK_ID = 918273645;

    protected $signature = 'deploy:migrate';

    protected $description = 'Run migrations, and seed on first deploy, holding a lock so concurrent instances cannot race';

    public function handle(): int
    {
        // Advisory locks are session scoped, so they only work on a direct
        // connection. Through a transaction pooler the session is not ours to
        // keep and the lock would be meaningless.
        $locking = DB::connection()->getDriverName() === 'pgsql';

        if ($locking) {
            $this->components->info('Waiting for the migration lock...');
            DB::statement('select pg_advisory_lock('.self::LOCK_ID.')');
        }

        try {
            if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
                return self::FAILURE;
            }

            // Per-table rather than all-or-nothing: a seeder that failed
            // halfway through a previous deploy leaves some tables empty, and
            // those still need filling on the next boot.
            return $this->call('deploy:seed');
        } finally {
            if ($locking) {
                DB::statement('select pg_advisory_unlock('.self::LOCK_ID.')');
            }
        }
    }
}
