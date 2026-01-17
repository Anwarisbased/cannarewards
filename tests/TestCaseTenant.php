<?php

namespace Tests;

use Illuminate\Support\Facades\Route;

class TestCaseTenant extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure tenant routes are loaded
        $this->loadTenantRoutes();
    }

    protected function loadTenantRoutes(): void
    {
        // Load tenant routes for testing
        if (file_exists(base_path('routes/tenant.php'))) {
            // Load the tenant routes with the web middleware only for testing
            Route::middleware(['web'])->group(base_path('routes/tenant.php'));
        }
    }
}
