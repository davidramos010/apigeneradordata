<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting all products
     */
    public function test_get_all_products()
    {
        Product::factory(5)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    /**
     * Test getting a single product
     */
    public function test_get_single_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('id', $product->id);
    }

    /**
     * Test getting non-existent product
     */
    public function test_get_non_existent_product()
    {
        $response = $this->getJson('/api/product/999');

        $response->assertStatus(404);
    }

    /**
     * Test creating a product with valid data
     */
    public function test_create_product_with_valid_data()
    {
        $data = [
            'name' => 'Test Product Name',
            'price' => 99.99,
        ];

        $response = $this->postJson('/api/product', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * Test creating a product with invalid data
     */
    public function test_create_product_with_invalid_data()
    {
        $data = [
            'name' => 'Invalid',
            'price' => -10,
        ];

        $response = $this->postJson('/api/product', $data);

        $response->assertStatus(422);
    }

    /**
     * Test updating a product
     */
    public function test_update_product()
    {
        $product = Product::factory()->create();
        
        $data = [
            'name' => 'Updated Product Name',
            'price' => 149.99,
        ];

        $response = $this->putJson("/api/product/{$product->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $data);
    }

    /**
     * Test deleting a product
     */
    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/product/{$product->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
