<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class TenantDatabaseSwitcher
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the tenant ID from the request headers
        $tenantId = $request->header('X-Tenant-ID');

        // Check if the tenant ID is provided
        if (!$tenantId) {
            return response()->json([
                'message' => 'Tenant ID not provided'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Retrieve the tenant using the tenant ID
        $tenant = Tenant::find($tenantId);

        // Check if the tenant exists
        if (!$tenant) {
            return response()->json([
                'error' => 'Tenant not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Set the tenant's database configuration
        Config::set('database.connections.tenant.database', $tenant->database_name);

        // Purge the tenant connection cache to avoid stale connections
        DB::purge('tenant');

        // Reconnect to the tenant's database
        DB::reconnect('tenant');

        return $next($request);
    }
}
