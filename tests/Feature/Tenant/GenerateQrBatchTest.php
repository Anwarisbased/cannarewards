<?php

namespace Tests\Feature\Tenant;

use App\Actions\Tenant\GenerateQrBatchAction;
use App\Models\Tenant as TenantModel;
use App\Models\Tenant\CommercialGood;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenerateQrBatchTest extends TestCase
{
    #[Test]
    public function it_generates_batch_and_uploads_csv()
    {
        // Setup: Initialize a Tenant ('dime')
        $tenant = TenantModel::find('dime');
        if (! $tenant) {
            $tenant = TenantModel::create([
                'id' => 'dime',
                'brand_name' => 'Dime Industries',
                'plan' => 'enterprise',
            ]);
        }

        // Create the tenant database if it doesn't exist
        $tenantDatabaseName = 'tenant'.$tenant->getTenantKey();
        $existingDatabase = DB::connection('mysql')->select('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?', [$tenantDatabaseName]);

        if (empty($existingDatabase)) {
            DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$tenantDatabaseName}`");
        }

        Storage::fake('s3');

        // CRITICAL FIX: Force the config to set the tenant database
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

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
            ]);
        });

        // Test Case: Run inside $tenant->run(function() { ... })
        $csvPath = null;
        $batchLabel = 'Test Batch';
        $tenant->run(function () use ($commercialGood, &$csvPath, $batchLabel) {
            // Clear any existing codes with this batch label
            \App\Models\Tenant\RewardCode::where('batch_id', $batchLabel)->delete();

            // Call (new GenerateQrBatchAction)->execute($product, 50, 'Test Batch')
            $action = new GenerateQrBatchAction;
            $csvPath = $action->execute($commercialGood, 50, $batchLabel);
        });

        // Assertions:
        // Assert reward_codes table count is 50
        $codeCount = 0;
        $tenant->run(function () use (&$codeCount, $batchLabel) {
            // Count only the codes created in this specific batch
            $codeCount = \App\Models\Tenant\RewardCode::where('batch_id', $batchLabel)->count();
        });
        $this->assertEquals(50, $codeCount);

        // Assert Storage::disk('s3')->exists(...) is true
        Storage::disk('s3')->assertExists($csvPath);

        // Assert the CSV content contains 50 rows (plus header)
        $csvContent = Storage::disk('s3')->get($csvPath);
        $rows = explode("\n", trim($csvContent));
        // Should have 51 rows: 1 header + 50 data rows
        $this->assertCount(51, $rows);
    }
}
