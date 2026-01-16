<?php

namespace Tests\Feature\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardCode;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeaserTest extends TestCase
{
    #[Test]
    public function guest_can_see_teaser_page_for_active_code()
    {
        // Setup: Initialize a Tenant ('dime')
        $tenant = Tenant::find('dime');
        if (! $tenant) {
            $tenant = Tenant::create([
                'id' => 'dime',
                'brand_name' => 'Dime Industries',
                'plan' => 'enterprise',
            ]);
        }

        // Create domain record for the tenant
        $domain = \Stancl\Tenancy\Database\Models\Domain::create([
            'domain' => 'dime.localhost',
            'tenant_id' => 'dime',
        ]);

        // Create the tenant database if it doesn't exist
        $tenantDatabaseName = 'tenant'.$tenant->getTenantKey();
        \Illuminate\Support\Facades\DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");

        // CRITICAL FIX: Force the config to set the tenant database
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Run tenant migrations to ensure tables exist
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Create a CommercialGood inside that tenant
        $commercialGood = null;
        $tenant->run(function () use (&$commercialGood) {
            $commercialGood = CommercialGood::create([
                'sku' => 'TEST-SKU-'.uniqid(),
                'name' => 'Test Product',
                'points_awarded' => 100,
                'msrp_cents' => 2000,
                'strain_type' => 'sativa',
                'image_url' => 'https://example.com/test-image.jpg',
            ]);
        });

        // Create an active reward code
        $rewardCode = null;
        $tenant->run(function () use ($commercialGood, &$rewardCode) {
            $rewardCode = RewardCode::create([
                'code' => str_pad('TC'.substr(md5(uniqid()), 0, 14), 16, '0'),
                'commercial_good_id' => $commercialGood->id,
                'batch_id' => 'BATCH-'.substr(md5(uniqid()), 0, 8),
                'status' => 'active',
            ]);
        });

        // Test accessing the teaser page for an active code
        $response = $this->withHeader('HOST', 'dime.localhost')->get("/claim/{$rewardCode->code}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Teaser')
            ->has('product')
            ->where('product.name', $commercialGood->name)
            ->where('product.points_awarded', $commercialGood->points_awarded)
        );
    }

    #[Test]
    public function guest_sees_error_page_for_used_code()
    {
        // Setup: Initialize a Tenant ('dime')
        $tenant = Tenant::find('dime');
        if (! $tenant) {
            $tenant = Tenant::create([
                'id' => 'dime',
                'brand_name' => 'Dime Industries',
                'plan' => 'enterprise',
            ]);
        }

        // Create domain record for the tenant (if not exists)
        if (! \Stancl\Tenancy\Database\Models\Domain::where('domain', 'dime.localhost')->exists()) {
            \Stancl\Tenancy\Database\Models\Domain::create([
                'domain' => 'dime.localhost',
                'tenant_id' => 'dime',
            ]);
        }

        // Create the tenant database if it doesn't exist
        $tenantDatabaseName = 'tenant'.$tenant->getTenantKey();
        \Illuminate\Support\Facades\DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");

        // CRITICAL FIX: Force the config to set the tenant database
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Run tenant migrations to ensure tables exist
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Create an used reward code
        $rewardCode = null;
        $tenant->run(function () use (&$rewardCode) {
            $rewardCode = RewardCode::create([
                'code' => str_pad('UC'.substr(md5(uniqid()), 0, 14), 16, '0'),
                'commercial_good_id' => 1, // Using a dummy ID since we're testing error cases
                'batch_id' => 'BATCH-'.substr(md5(uniqid()), 0, 8),
                'status' => 'used',
            ]);
        });

        // Test accessing the teaser page for a used code
        $response = $this->withHeader('HOST', 'dime.localhost')->get("/claim/{$rewardCode->code}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Error')
            ->where('reason', 'used')
        );
    }

    #[Test]
    public function guest_sees_error_page_for_void_code()
    {
        // Setup: Initialize a Tenant ('dime')
        $tenant = Tenant::find('dime');
        if (! $tenant) {
            $tenant = Tenant::create([
                'id' => 'dime',
                'brand_name' => 'Dime Industries',
                'plan' => 'enterprise',
            ]);
        }

        // Create domain record for the tenant (if not exists)
        if (! \Stancl\Tenancy\Database\Models\Domain::where('domain', 'dime.localhost')->exists()) {
            \Stancl\Tenancy\Database\Models\Domain::create([
                'domain' => 'dime.localhost',
                'tenant_id' => 'dime',
            ]);
        }

        // Create the tenant database if it doesn't exist
        $tenantDatabaseName = 'tenant'.$tenant->getTenantKey();
        \Illuminate\Support\Facades\DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");

        // CRITICAL FIX: Force the config to set the tenant database
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Run tenant migrations to ensure tables exist
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Create a void reward code
        $rewardCode = null;
        $tenant->run(function () use (&$rewardCode) {
            $rewardCode = RewardCode::create([
                'code' => str_pad('VC'.substr(md5(uniqid()), 0, 14), 16, '0'),
                'commercial_good_id' => 1, // Using a dummy ID since we're testing error cases
                'batch_id' => 'BATCH-'.substr(md5(uniqid()), 0, 8),
                'status' => 'void',
            ]);
        });

        // Test accessing the teaser page for a void code
        $response = $this->withHeader('HOST', 'dime.localhost')->get("/claim/{$rewardCode->code}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Error')
            ->where('reason', 'invalid')
        );
    }

    #[Test]
    public function guest_sees_error_page_for_nonexistent_code()
    {
        // Setup: Initialize a Tenant ('dime')
        $tenant = Tenant::find('dime');
        if (! $tenant) {
            $tenant = Tenant::create([
                'id' => 'dime',
                'brand_name' => 'Dime Industries',
                'plan' => 'enterprise',
            ]);
        }

        // Create domain record for the tenant (if not exists)
        if (! \Stancl\Tenancy\Database\Models\Domain::where('domain', 'dime.localhost')->exists()) {
            \Stancl\Tenancy\Database\Models\Domain::create([
                'domain' => 'dime.localhost',
                'tenant_id' => 'dime',
            ]);
        }

        // Create the tenant database if it doesn't exist
        $tenantDatabaseName = 'tenant'.$tenant->getTenantKey();
        \Illuminate\Support\Facades\DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");

        // CRITICAL FIX: Force the config to set the tenant database
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        \Illuminate\Support\Facades\DB::purge('tenant');
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Run tenant migrations to ensure tables exist
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // Test accessing the teaser page for a non-existent code
        $response = $this->withHeader('HOST', 'dime.localhost')->get('/claim/NONEXISTENT-CODE');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Error')
            ->where('reason', 'invalid')
        );
    }
}
