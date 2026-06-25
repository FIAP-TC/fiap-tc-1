<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_application_returns_ok(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
