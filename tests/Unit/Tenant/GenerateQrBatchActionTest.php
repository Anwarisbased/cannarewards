<?php

namespace Tests\Unit\Tenant;

use App\Actions\Tenant\GenerateQrBatchAction;
use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GenerateQrBatchActionTest extends TestCase
{
    #[Test]
    public function test_it_generates_codes_and_uploads_csv()
    {
        // 1. Setup Tenant (using the fix from previous task)
        $tenantId = 'test_qr_tenant';

        // Cleanup
        if ($t = Tenant::find($tenantId)) {
            $t->delete();
        }
        \Illuminate\Support\Facades\DB::connection('mysql')->statement("DROP DATABASE IF EXISTS tenant{$tenantId}");

        $tenant = Tenant::create([
            'id' => $tenantId,
            'brand_name' => 'QR Brand',
            'plan' => 'growth',
        ]);

        $tenant->database()->manager()->createDatabase($tenant);
        tenancy()->initialize($tenant);

        // FIX: Manual override for TenantConnection trait in test environment
        config(['database.connections.tenant.database' => $tenant->database()->getName()]);
        \Illuminate\Support\Facades\DB::purge('tenant');

        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        // 2. Setup Data
        $good = CommercialGood::create([
            'sku' => 'QR-TEST-GOOD',
            'name' => 'QR Test Good',
            'points_awarded' => 100,
            'msrp_cents' => 1000,
            'strain_type' => 'hybrid',
            'is_active' => true,
        ]);

        // Configure and fake the S3 disk properly
        config([
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'key' => env('AWS_ACCESS_KEY_ID', ''),
                'secret' => env('AWS_SECRET_ACCESS_KEY', ''),
                'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'bucket' => env('AWS_BUCKET', 'test-bucket'),
                'url' => env('AWS_URL'),
                'endpoint' => env('AWS_ENDPOINT'),
                'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
                'throw' => false,
            ]
        ]);

        Storage::fake('s3');

        // 3. Execute Action
        $action = new GenerateQrBatchAction;
        $batchLabel = 'BATCH-TEST-001';
        $quantity = 50;

        $filePath = $action->execute($good, $quantity, $batchLabel);

        // 4. Verification

        // DB Assertion
        $this->assertEquals($quantity, RewardCode::where('batch_id', $batchLabel)->count());
        $this->assertDatabaseHas('reward_codes', [
            'batch_id' => $batchLabel,
            'commercial_good_id' => $good->id,
        ], 'tenant');

        // Storage Assertion
        Storage::disk('s3')->assertExists($filePath);

        // Content Assertion
        $content = Storage::disk('s3')->get($filePath);
        $this->assertStringContainsString('code,url', $content); // Header

        // Check a random code exists in CSV
        $randomCode = RewardCode::first()->code;
        $this->assertStringContainsString($randomCode, $content);

        // Cleanup (Automated by RefreshDatabase mostly, but good practice for tenant DBs)
    }
}
