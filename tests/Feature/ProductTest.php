<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Tenant;

class ProductTest extends TestCase
{
    // use RefreshDatabase;

    public function test_add_product()
    {
        $tenant = Tenant::orderBy('id', 'asc')->first();

        $response = $this->withHeader('X-Tenant-ID', $tenant->id)
            ->postJson('/api/products', [
                'name' => 'Test Product',
                'description' => 'This is a test product',
                'price' => 99.99,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test Product',
                'price' => 99.99,
            ]);
    }
}
