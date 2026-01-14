<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class CreateTenantDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Tenant $tenant
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Run the tenant-specific migrations using tenancy context
        $tenantId = $this->tenant->getTenantKey();
        tenancy()->runForMultiple(collect([$tenantId]), function () {
            // Run the tenant-specific migrations
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true, // Required for production environments
            ]);

            // Optionally, seed the tenant database
            Artisan::call('db:seed', [
                '--force' => true,
            ]);
        });
    }
}
