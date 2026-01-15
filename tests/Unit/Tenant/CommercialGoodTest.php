<?php

namespace Tests\Unit\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CommercialGoodTest extends TestCase
{
    #[Test]
    public function test_commercial_good_can_be_created_in_tenant_context()
    {
        // Cleanup from previous runs (necessary because createDatabase causes implicit commit)
        if ($t = Tenant::find('test_tenant')) {
            $t->delete();
        }
        \Illuminate\Support\Facades\DB::connection('mysql')->statement('DROP DATABASE IF EXISTS tenanttest_tenant');

        $tenant = Tenant::create([
            'id' => 'test_tenant',
            'brand_name' => 'Test Brand',
            'plan' => 'starter',
        ]);

        // Manual trigger since event isn't firing in test env
        // This causes implicit commit!
        $tenant->database()->manager()->createDatabase($tenant);

        tenancy()->initialize($tenant);

        // Manual override for TenantConnection trait
        config(['database.connections.tenant.database' => $tenant->database()->getName()]);
        \Illuminate\Support\Facades\DB::purge('tenant');

        // Run migrations on the tenant connection
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant', // Explicitly migrate the tenant connection
            '--force' => true,
        ]);

        $good = CommercialGood::create([
            'sku' => 'TEST-SKU-001',
            'name' => 'Test Product',
            'points_awarded' => 100,
            'msrp_cents' => 2000,
            'strain_type' => 'sativa',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('commercial_goods', [
            'sku' => 'TEST-SKU-001',
        ], 'tenant');

        // Verify RewardCode relationship
        $code = \App\Models\Tenant\RewardCode::create([
            'code' => 'TEST-Reward-123',
            'commercial_good_id' => $good->id,
            'batch_id' => 'BATCH-001',
            'status' => 'active',
        ]);

        // dump($code->toArray());

        $this->assertDatabaseHas('reward_codes', [
            'code' => 'TEST-Reward-123',
            'commercial_good_id' => $good->id,
        ], 'tenant');

        $codeWithRelationship = \App\Models\Tenant\RewardCode::with('commercialGood')->find($code->code);
        $this->assertEquals($good->id, $codeWithRelationship->commercialGood->getAttribute('id'));
    }
}
