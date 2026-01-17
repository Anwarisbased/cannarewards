<?php

namespace Tests\Feature\Tenant;

use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCaseTenant; // Use TestCaseTenant to load tenant routes
use Tests\Traits\SetUpTenantTest;

class TeaserTest extends TestCaseTenant
{
    // 1. RefreshDatabase handles transactions (rolling back data)
    // 2. SetUpTenantTest handles creating the Physical DB + Domain Record
    use RefreshDatabase, SetUpTenantTest;

    protected function setUp(): void
    {
        parent::setUp();

        // This creates the Tenant 'dime' and Domain 'dime.localhost'
        // It ALSO creates the physical database 'tenantdime'
        $this->setUpTenant('dime', 'dime.localhost');
    }

    #[Test]
    public function guest_can_see_teaser_page_for_active_code()
    {
        // 1. PREPARE DATA
        // tenancy is already initialized by setUpTenant, so we can seed data directly
        $tenant = Tenant::find('dime');

        $good = CommercialGood::create([
            'sku' => 'TEST-SKU-001',
            'name' => 'Dime OG',
            'points_awarded' => 100,
            'msrp_cents' => 2000,
            'strain_type' => 'sativa',
            'image_url' => 'https://example.com/test.jpg',
            'is_active' => true,
        ]);

        $code = RewardCode::create([
            'code' => 'ACTIVE1234567890',
            'commercial_good_id' => $good->id,
            'batch_id' => \Illuminate\Support\Str::uuid(),
            'status' => 'active',
        ]);

        // 2. EXECUTE REQUEST
        // We DO NOT disable middleware. We act like a browser.
        // We use the full URL so Laravel sets the Host header automatically.
        $response = $this->get("http://dime.localhost/claim/{$code->code}");

        // 3. ASSERT
        $response->assertOk(); // 200 OK

        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Teaser')
            ->where('product.name', 'Dime OG')
            ->where('product.points_awarded', 100)
        );
    }

    #[Test]
    public function guest_sees_error_page_for_used_code()
    {
        $good = CommercialGood::create([
            'sku' => 'TEST-USED',
            'name' => 'Used Item',
            'points_awarded' => 100,
            'msrp_cents' => 100,
            'strain_type' => 'hybrid',
            'is_active' => true,
        ]);

        $code = RewardCode::create([
            'code' => 'USEDCODE12345678',
            'commercial_good_id' => $good->id,
            'batch_id' => \Illuminate\Support\Str::uuid(),
            'status' => 'used',
        ]);

        // Act like a browser
        $response = $this->get("http://dime.localhost/claim/{$code->code}");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Error')
            ->where('reason', 'used')
        );
    }

    #[Test]
    public function guest_sees_error_page_for_invalid_code()
    {
        $response = $this->get('http://dime.localhost/claim/INVALIDCODE123');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Error')
            ->where('reason', 'invalid')
        );
    }
}
