<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    #[Test]
    public function test_the_application_redirects_to_admin_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('filament.admin.pages.dashboard'));
    }
}
