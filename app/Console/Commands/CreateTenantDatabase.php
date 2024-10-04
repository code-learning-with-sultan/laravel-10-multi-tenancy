<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant database and run tenant-specific migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $database = $this->argument('database');

        try {
            // Validate that the database does not already exist
            if ($this->databaseExists($database)) {
                $this->error("The database '{$database}' already exists.");
                return 1;
            }

            // Create the tenant record in the master database
            $tenant = Tenant::create([
                'name' => $name,
                'database_name' => $database,
            ]);

            // Create the new database for the tenant with backticks
            DB::statement("CREATE DATABASE `{$database}`");
            $this->info("Database '{$database}' created.");

            // Set the tenant database name in the configuration
            Config::set('database.connections.tenant.database', $database);

            // Run the tenant-specific migrations
            Artisan::call('migrate', [
                '--database' => 'tenant', // Use the tenant connection defined in config/database.php
                '--path' => 'database/migrations/tenant', // Path to tenant-specific migrations
                '--force' => true,
            ]);

            // Provide success feedback in the console
            $this->info("Migrations for tenant '{$name}' successfully executed.");
            $this->info("Tenant '{$name}' with database '{$database}' created successfully.");
        } catch (Exception $e) {
            // Log the error for debugging
            $this->error("Error while creating tenant '{$name}': " . $e->getMessage());

            // If an error occurs, remove the tenant record and rollback database creation
            if (isset($tenant)) {
                $tenant->delete();
            }

            // Drop the created database if it exists (cleanup)
            if ($this->databaseExists($database)) {
                DB::statement("DROP DATABASE `{$database}`");
                $this->info("Database '{$database}' dropped due to failure.");
            }

            // Show the error message to the console
            $this->error("Failed to create tenant '{$name}': " . $e->getMessage());

            return 1; // Exit with a non-zero status code
        }

        return 0; // Successful execution
    }

    /**
     * Check if a database already exists.
     *
     * @param  string  $database
     * @return bool
     */
    protected function databaseExists($database)
    {
        $result = DB::select("SHOW DATABASES LIKE '%$database%'");
        return count($result) > 0;
    }
}
