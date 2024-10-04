<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeTenantMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migration {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration for tenant databases in the tenant migrations folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the migration name from the input
        $name = $this->argument('name');

        // Call the artisan make:migration command with the custom path
        Artisan::call('make:migration', [
            'name' => $name,
            '--path' => 'database/migrations/tenant'
        ]);

        // Output success message
        $this->info("Tenant migration '$name' created successfully in 'database/migrations/tenant'.");
    }
}
