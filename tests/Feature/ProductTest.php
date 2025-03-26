<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    // Optional: Make sure to reset the database before each test
   // $this->seed();  // Or use RefreshDatabase trait
    $this->refreshApplication();
});


it('can fetch a single product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson(route('api.v1.products.show', $product->id));

    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonFragment(['name' => $product->name]);
});

it('can create a product', function () {
    $product = [
        'name' => 'New Product',
        'description' => 'Description of new product',
        'price' => 100.0,
        'category' => 'Category A',
        'image_url' => null
    ];

    $response = $this->postJson(route('api.v1.products.store'), $product);

    // Assertions
    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonFragment($product);
    $this->assertDatabaseHas('products', ['name' => 'New Product']);
});

it('can update a product', function () {
    $product = Product::factory()->create();

    $updatedData = [
        'name' => 'Updated Product Name',
        'description' => 'Updated product description',
        'price' => 200.0,
        'category' => 'Category B',
        'image_url' => null,
    ];

    $response = $this->putJson(route('api.v1.products.update', $product->id), $updatedData);

    // Assertions
    $response->assertStatus(Response::HTTP_OK);
    $response->assertJsonFragment($updatedData);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product Name'
    ]);
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson(route('api.v1.products.destroy', $product->id));

    $response->assertStatus(Response::HTTP_NO_CONTENT);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('requires valid data to create a product', function () {
    $invalidData = [
        'name' => '',
        'description' => 'Description without a name',
        'price' => 'not-a-number',
        'category' => '',
        'image_url' => null
    ];

    $response = $this->postJson(route('api.v1.products.store'), $invalidData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['name', 'price', 'category']);
});
