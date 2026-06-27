<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_api_test_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/test');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }
}
