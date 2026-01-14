#!/usr/bin/env php
<?php

require_once 'vendor/autoload.php';

use App\Actions\Tenant\GenerateQrBatchAction;
use App\Models\Tenant;
use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardCode;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find the dime tenant
$dime = Tenant::find('dime');
if (! $dime) {
    echo "Dime tenant not found\n";
    exit(1);
}

// Initialize the dime tenant
tenancy()->initialize($dime);

// Find a commercial good or create one if none exists
$good = CommercialGood::first();
if (! $good) {
    $good = CommercialGood::create([
        'sku' => 'TEST-SKU-001',
        'name' => 'Test Product',
        'points_awarded' => 100,
        'msrp_cents' => 10000,
        'strain_type' => 'hybrid',
        'is_active' => true,
        'image_url' => 'https://example.com/test.jpg',
    ]);
    echo "Created test commercial good\n";
}

echo 'Using commercial good: '.$good->name."\n";

// Run the action
$action = new GenerateQrBatchAction;
$result = $action->execute($good, 100, 'Test Batch');

echo 'Generated QR batch result: '.$result."\n";

// Check the database
$count = RewardCode::where('batch_id', 'Test Batch')->count();
echo 'Number of reward codes in database: '.$count."\n";

if ($count === 100) {
    echo "✓ Successfully created 100 reward codes in the database\n";
} else {
    echo '✗ Expected 100 reward codes, but found '.$count."\n";
}

echo "Verification completed.\n";
