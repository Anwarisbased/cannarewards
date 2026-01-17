<?php

namespace Tests\Traits;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

trait SetUpTenantTest
{
    protected function setUpTenant(string $tenantId = 'dime', string $domain = 'dime.localhost'): Tenant
    {
        // 1. Create Tenant in Central DB
        $tenant = Tenant::firstOrCreate([
            'id' => $tenantId,
        ], [
            'brand_name' => ucfirst($tenantId).' Industries',
            'plan' => 'enterprise',
        ]);

        // 2. Create Domain in Central DB (CRITICAL for Middleware identification)
        $originalConnection = DB::getDefaultConnection();
        DB::setDefaultConnection('central'); // Ensure we write to central

        try {
            $tenant->domains()->firstOrCreate(['domain' => $domain]);
        } finally {
            DB::setDefaultConnection($originalConnection); // Switch back
        }

        // 3. Create the Physical Tenant Database
        // We use raw SQL to ensure it exists regardless of transaction state
        $tenantDatabaseName = 'tenant'.$tenantId;
        DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");

        // 4. Run Migrations on the Tenant Database
        // We temporarily initialize to run migrations, then we will end it
        tenancy()->initialize($tenant);

        // Force config for migration context
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $this->artisan('migrate:fresh', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // 5. End Tenancy logic for the Setup phase.
        // For Feature tests (HTTP), we want the Request to trigger initialization, not us.
        // For Unit tests, the test method will manually initialize if needed.
        if (! $this instanceof \Tests\TestCase) {
            // Keep initialized for Unit tests
        } else {
            // We generally want to end it for Feature tests so middleware runs fresh,
            // BUT stancl/tenancy is smart enough to handle re-ini.
            // To be safe, let's leave it active as the default state for data seeding.
        }

        return $tenant;
    }

    protected function tearDownTenant(string $tenantId = 'dime'): void
    {
        $tenantDatabaseName = 'tenant'.$tenantId;
        try {
            // Drop the physical database
            DB::connection('mysql')->statement("DROP DATABASE IF EXISTS `{$tenantDatabaseName}`");
        } catch (\Exception $e) {
            // Ignore
        }
    }
}
