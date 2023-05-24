<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->fillDBWithProducts();
        $this->assertDatabaseCount(Product::class, 3);
        $this->assertDatabaseCount(ProductPrice::class, 9);
    }

    public function test_it_will_return_all_products_with_prices(): void
    {
        $response = $this->get('/api/v1/products');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data')
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json
                            ->has('id')
                            ->has('name')
                            ->has('description')
                            ->has('prices', 3)
                            ->etc()
                    )   
                    ->has('data.0.prices.0', fn (AssertableJson $json) =>
                        $json
                            ->has('value')
                            ->has('price')
                            ->etc()
                    )
                    ->has('links')
                    ->has('meta')
                    ->where('meta.per_page', 10)
                    ->where('meta.total', 3)
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_filtered_by_name(): void
    {
        $filters = [
            'name' => 'Product 3'
        ];

        $this->assertDatabaseHas(Product::class, [
            'name' => $filters['name'],
        ]);

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json
                            ->where('name', $filters['name'])
                            ->etc()
                    )   
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_filtered_by_description(): void
    {
        $filters = [
            'description' => 'Extra'
        ];

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json
                            ->where('description', 'Extra product')
                            ->etc()
                    )   
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_filtered_by_min_price(): void
    {
        $filters = [
            'price-min' => 400
        ];

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data.0.prices.1', fn (AssertableJson $json) =>
                        $json
                            ->where('value', $filters['price-min'])
                            ->etc()
                    )   
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_filtered_by_max_price(): void
    {
        $filters = [
            'price-max' => 50
        ];

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data.0.prices.0', fn (AssertableJson $json) =>
                        $json
                            ->where('value', $filters['price-max'])
                            ->etc()
                    )   
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_sorted_by_key(): void
    {
        $filters = [
            'sortBy' => 'name',
            'sortDirection' => 'desc',
        ];

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data.0', fn (AssertableJson $json) =>
                        $json
                            ->where('name', 'Product 3')
                            ->etc()
                    )   
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_products_paginated_by_value(): void
    {
        $filters = [
            'paginate' => 1,
        ];

        $response = $this->get('/api/v1/products?' . http_build_query($filters));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('meta')
                    ->where('meta.from', 1)
                    ->where('meta.to', 1)
                    ->where('meta.current_page', 1)
                    ->where('meta.last_page', 3)
                    ->where('meta.per_page', 1)
                    ->where('meta.total', 3)
                    ->etc()
            )
        ;
    }

    public function test_it_will_return_requested_product_info(): void
    {
        $product = Product::all()->first();
        $productId = $product->id;

        $response = $this->get('/api/v1/products/' . $productId);

        $response
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data')
                    ->has('data.id')
                    ->has('data.name')
                    ->has('data.description')
                    ->has('data.prices', 3)
                    ->has('data.prices.0', fn (AssertableJson $json) =>
                        $json
                            ->has('value')
                            ->has('price')
                            ->etc()
                    )
                    ->etc()
            )
        ;
    }

    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $productData = [
            'name' => 'Product 4',
            'description' => 'Secret Product',
        ];

        $response = $this->post('/api/v1/products', $productData, ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_product_with_valid_data(): void
    {
        $this->assertDatabaseCount(Product::class, 3);

        $productData = [
            'name' => 'Product 4',
            'description' => 'Secret Product',
        ];

        $userRegisterResponse = $this->post('/api/v1/register', [
            'name' => 'test_user', 
            'email' => 'test_user@example.com', 
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response = $this->withToken($userRegisterResponse['token'])->post('/api/v1/products', $productData, ['Accept' => 'application/json']);

        $response->assertCreated();

        $this->assertDatabaseCount(Product::class, 4);
        $this->assertDatabaseHas(Product::class, $productData);
    }

    public function test_unauthenticated_user_cannot_update_product(): void
    {
        $product = Product::all()->first();
        $productId = $product->id;

        $productData = [
            'name' => 'Product 1 Updated',
        ];

        $response = $this->put('/api/v1/products/' . $productId, $productData, ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_product_with_valid_data(): void
    {
        $product = Product::all()->first();
        $productId = $product->id;

        $productData = [
            'name' => 'Product 1 Updated',
        ];

        $userRegisterResponse = $this->post('/api/v1/register', [
            'name' => 'test_user', 
            'email' => 'test_user@example.com', 
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response = $this->withToken($userRegisterResponse['token'])->put('/api/v1/products/' . $productId, $productData, ['Accept' => 'application/json']);

        $response->assertOk();

        $this->assertDatabaseCount(Product::class, 3);
        $this->assertDatabaseHas(Product::class, $productData);
    }

    public function test_unauthenticated_user_cannot_delete_product(): void
    {
        $product = Product::all()->first();
        $productId = $product->id;

        $response = $this->delete('/api/v1/products/' . $productId, [], ['Accept' => 'application/json']);

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_delete_product(): void
    {
        $product = Product::all()->first();
        $productId = $product->id;

        $userRegisterResponse = $this->post('/api/v1/register', [
            'name' => 'test_user', 
            'email' => 'test_user@example.com', 
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response = $this->withToken($userRegisterResponse['token'])->delete('/api/v1/products/' . $productId, [], ['Accept' => 'application/json']);

        $response->assertNoContent();

        $this->assertDatabaseCount(Product::class, 2);
        $this->assertDatabaseMissing(Product::class, [
            'id' => $productId,
            'name' => $product->name,
        ]);
    }
}
