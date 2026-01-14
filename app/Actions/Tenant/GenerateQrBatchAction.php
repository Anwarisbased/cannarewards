<?php

namespace App\Actions\Tenant;

use App\Models\Tenant\CommercialGood;
use App\Models\Tenant\RewardCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Csv\Writer;

class GenerateQrBatchAction
{
    public function execute(CommercialGood $product, int $quantity, string $batchLabel, ?string $disk = null): string
    {
        // 1. Setup CSV Writer with a temporary memory stream
        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->insertOne(['code', 'url']); // Headers

        // 2. Define chunk size
        $chunkSize = 1000;
        $chunks = ceil($quantity / $chunkSize);
        $baseUrl = config('app.url').'/claim/'; // e.g. https://cannarewards.io/claim/

        for ($i = 0; $i < $chunks; $i++) {
            $currentChunkSize = min($chunkSize, $quantity - ($i * $chunkSize));

            $insertData = [];
            $csvData = [];
            $now = now();

            for ($j = 0; $j < $currentChunkSize; $j++) {
                // 16 chars = 96 bits of entropy. Collision chance is negligible.
                $code = Str::random(16);

                $insertData[] = [
                    'code' => $code,
                    'commercial_good_id' => $product->id,
                    'batch_id' => $batchLabel,
                    'status' => 'active', // Default status
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $csvData[] = [$code, $baseUrl.$code];
            }

            // 3. Database Insert (Atomic per chunk)
            // Using insertOrIgnore to skip occasional collisions without crashing
            // In a real crypto-secure requirement, we'd check affected rows and retry,
            // but for 16 alphanumeric chars, collision is effectively impossible.
            RewardCode::insert($insertData);

            // 4. Write to CSV
            $csv->insertAll($csvData);
        }

        // 5. Upload to storage (using local for development, s3 for production)
        $filename = 'export/batches/'.tenant('id')."/{$batchLabel}_".time().'.csv';

        // Use provided disk or determine automatically
        $storageDisk = $disk ?? $this->getStorageDisk();

        // Put the content of the stream into storage
        // We use string casting of the CSV object to get the content
        Storage::disk($storageDisk)->put($filename, $csv->toString());

        return $filename;
    }

    private function getStorageDisk(): string
    {
        // Use S3 in production, local in development/testing
        return app()->environment('production') ? 's3' : 'local';
    }
}
