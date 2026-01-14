<?php

namespace App\Filament\Resources\TenantResource\Pages;

use App\Filament\Resources\TenantResource;
use App\Jobs\CreateTenantDatabaseJob;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Tenant
    {
        // Extract the fields that should go into the main tenant record
        $id = $data['id'];
        $brand_name = $data['brand_name'] ?? '';
        $plan = $data['plan'] ?? 'enterprise';
        $config = $data['config'] ?? [];

        // Prepare the data for the tenant record
        // For stancl/tenancy, we need to store custom fields in the 'data' JSON column
        $tenantData = [
            'brand_name' => $brand_name,
            'plan' => $plan,
            'config' => $config,
        ];

        // Create the tenant record directly using DB to ensure proper insertion
        \DB::table('tenants')->insert([
            'id' => $id,
            'brand_name' => $brand_name,
            'plan' => $plan,
            'config' => json_encode($config), // Convert array to JSON string
            'data' => json_encode($tenantData), // Convert array to JSON string
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Retrieve the created tenant
        $tenant = Tenant::where('id', $id)->first();

        return $tenant;
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\Tenant $tenant */
        $tenant = $this->getRecord();

        // Create the associated domain record using the relationship
        $tenant->domains()->create([
            'domain' => $tenant->getAttribute('id').'.rewards.io', // Using the tenant ID as subdomain
        ]);

        // Dispatch the job to create and migrate the tenant database
        CreateTenantDatabaseJob::dispatch($tenant);
    }
}
