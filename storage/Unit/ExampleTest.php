<?php

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('it can create a product', function () {
    $data = [
        'name' => 'Sample Product',
        'description' => 'This is a test product.',
        'price' => 99.99,
        'category' => 'Electronics',
        'image' => UploadedFile::fake()->image('product.jpg'),
    ];

    $response = $this->postJson('/api/products', $data);

    $response->assertStatus(201)
        ->assertJsonPath('name', $data['name']);
});

test('it can retrieve a product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJsonPath('id', $product->id);
});

test('it can update a product', function () {
    $product = Product::factory()->create();
    $updatedData = ['name' => 'Updated Product'];

    $response = $this->putJson("/api/products/{$product->id}", $updatedData);

    $response->assertStatus(200)
        ->assertJsonPath('name', 'Updated Product');
});

test('it can delete a product', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
