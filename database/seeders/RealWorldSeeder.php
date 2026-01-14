<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardItem;
use Illuminate\Database\Seeder;

class RealWorldSeeder extends Seeder
{
    public function run(): void
    {
        // Create Tenant A: "Dime Industries"
        $dime = Tenant::create([
            'id' => 'dime',
            'brand_name' => 'Dime Industries',
            'plan' => 'enterprise',
            'config' => [
                'theme' => [
                    'primary_color' => '#C6A355',
                    'font_family' => 'Inter',
                    'radius' => '0.5rem',
                ],
                'copy' => [
                    'points_label' => 'Coins',
                    'scan_cta' => 'Stack Up',
                ],
                'features' => [
                    'referrals_enabled' => true,
                    'age_gate_strict' => true,
                ],
            ],
        ]);

        // Create database for dime tenant
        $dime->database()->manager()->createDatabase($dime);

        // Switch to dime tenant context to populate its tables
        tenancy()->initialize($dime);

        // Create 50 products for Dime
        for ($i = 1; $i <= 50; $i++) {
            CommercialGood::create([
                'sku' => 'DIME-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Dime Product #'.$i,
                'points_awarded' => rand(50, 200),
                'msrp_cents' => rand(500, 5000),
                'strain_type' => ['indica', 'sativa', 'hybrid', 'cbd'][array_rand(['indica', 'sativa', 'hybrid', 'cbd'])],
                'image_url' => 'https://example.com/products/dime-'.$i.'.jpg',
                'is_active' => true,
            ]);
        }

        // Create reward items for Dime
        $ranks = ['member' => 0, 'silver' => 500, 'gold' => 2000, 'rose_gold' => 10000];

        foreach ($ranks as $rank => $threshold) {
            RewardItem::create([
                'name' => ucfirst($rank).' Tier Reward',
                'points_cost' => $threshold,
                'cost_basis_cents' => 0,
                'stock_status' => 'in_stock',
                'required_rank' => $rank !== 'member' ? $rank : null,
                'is_featured' => false,
            ]);
        }

        // Create Tenant B: "Cookies"
        $cookies = Tenant::create([
            'id' => 'cookies',
            'brand_name' => 'Cookies',
            'plan' => 'growth',
            'config' => [
                'theme' => [
                    'primary_color' => '#007AFF',
                    'font_family' => 'Poppins',
                    'radius' => '0.5rem',
                ],
                'copy' => [
                    'points_label' => 'Cookies',
                    'scan_cta' => 'Get Rewards',
                ],
                'features' => [
                    'referrals_enabled' => true,
                    'age_gate_strict' => true,
                ],
            ],
        ]);

        // Create database for cookies tenant
        $cookies->database()->manager()->createDatabase($cookies);

        // Switch to cookies tenant context
        tenancy()->initialize($cookies);

        // Create 10 products for Cookies
        for ($i = 1; $i <= 10; $i++) {
            CommercialGood::create([
                'sku' => 'COOK-'.str_pad($i, 3, '0', STR_PAD_LEFT),
                'name' => 'Cookies Product #'.$i,
                'points_awarded' => rand(75, 150),
                'msrp_cents' => rand(1000, 8000),
                'strain_type' => ['flower', 'pre-roll'][array_rand(['flower', 'pre-roll'])],
                'image_url' => 'https://example.com/products/cookies-'.$i.'.jpg',
                'is_active' => true,
            ]);
        }

        // Create reward items for Cookies
        $cookieRanks = ['scout' => 0, 'leader' => 1000];

        foreach ($cookieRanks as $rank => $threshold) {
            RewardItem::create([
                'name' => ucfirst($rank).' Tier Reward',
                'points_cost' => $threshold,
                'cost_basis_cents' => 0,
                'stock_status' => 'in_stock',
                'required_rank' => $rank !== 'scout' ? $rank : null,
                'is_featured' => false,
            ]);
        }

        // Switch back to central context
        tenancy()->end();
    }
}
