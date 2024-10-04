<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class RunTenantMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve all tenants from the master database
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Set the tenant's database configuration
            Config::set('database.connections.tenant.database', $tenant->database_name);

            // Set the default connection to 'tenant'
            DB::purge('tenant');  // Purge the existing tenant connection
            DB::reconnect('tenant');  // Reconnect to the tenant database
            DB::setDefaultConnection('tenant');  // Set tenant as the default connection

            // Run the migrations for the tenant's database
            $this->info("Migrating for tenant: {$tenant->name}");

            Artisan::call('migrate', [
                '--database' => 'tenant',  // Specify the tenant database connection
                '--path' => 'database/migrations/tenant',  // Specify the tenant migrations path
                '--force' => true, // Ensure migration is run without user interaction
            ]);

            $this->info("Migrations completed for tenant: {$tenant->name}");
        }

        $this->info('All tenant migrations completed successfully.');
    }
}
