<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset';
    protected $description = 'Reset demo database dengan data fresh (simulasi 7 hari)';

    public function handle(): int
    {
        if (!config('app.demo_mode')) {
            $this->error('Demo mode tidak aktif. Set DEMO_MODE=true di .env');
            return 1;
        }

        $this->info('🔄 Resetting demo database...');

        // Fresh migrate + seed
        Artisan::call('migrate:fresh', [
            '--force' => true,
            '--no-interaction' => true,
        ]);
        $this->info('✅ Migration complete');

        Artisan::call('db:seed', [
            '--force' => true,
            '--no-interaction' => true,
        ]);
        $this->info('✅ Seeding complete');

        // Clear caches
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('route:cache');

        $this->info('🎉 Demo database reset successfully!');
        $this->info('   Next reset in ' . config('app.demo_reset_hours', 2) . ' hours.');

        return 0;
    }
}
