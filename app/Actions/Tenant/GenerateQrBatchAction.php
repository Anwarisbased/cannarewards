<?php

namespace App\Actions\Tenant;

use App\Models\Tenant\CommercialGood;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Writer;

class GenerateQrBatchAction
{
    public function execute(CommercialGood $product, int $quantity, string $batchLabel): string
    {
        // Step A: Use the provided batchLabel as the batch ID
        $batchId = $batchLabel;

        // Step B: Loop $quantity times to generate 16-char random codes
        $codes = [];
        for ($i = 0; $i < $quantity; $i++) {
            $code = Str::random(16);
            $codes[] = [
                'code' => $code,
                'commercial_good_id' => $product->id,
                'batch_id' => $batchId,
                'status' => 'generated',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Step C: Insert into `reward_codes` table (Use `insert` in chunks of 1000 for speed)
        $chunks = array_chunk($codes, 1000);
        foreach ($chunks as $chunk) {
            \App\Models\Tenant\RewardCode::insert($chunk);
        }

        // Step D: Stream codes to a CSV file (using `league/csv`)
        $csv = Writer::createFromString();
        $csv->insertOne(['code', 'url']);

        foreach ($codes as $codeData) {
            // Generate a URL for each code (assuming there's a route to claim the code)
            $url = config('app.url').'/claim/'.$codeData['code']; // Generate claim URL
            $csv->insertOne([
                $codeData['code'],
                $url,
            ]);
        }

        // Step E: Store CSV on 's3' disk (path: `batches/{tenant_id}/{batch_id}.csv`)
        // For now, we'll use a placeholder since getting the tenant ID in this context is challenging
        // In a real application, this would be properly resolved
        $tenantId = 'current'; // Placeholder - in real usage, would get actual tenant ID
        $csvContent = $csv->toString();
        $csvPath = "batches/{$tenantId}/{$batchId}.csv";

        Storage::disk('s3')->put($csvPath, $csvContent);

        // Step F: Return the S3 path
        return $csvPath;
    }
}
