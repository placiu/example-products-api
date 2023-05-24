<?php

namespace Tests;

use App\Models\Product;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function fillDBWithProducts(): void
    {
        $product1 = Product::factory()->create([
            'name' => 'Product 1',
            'description' => 'Super product',
        ]);

        $product2 = Product::factory()->create([
            'name' => 'Product 2',
            'description' => 'Extra product',
        ]);

        $product3 = Product::factory()->create([
            'name' => 'Product 3',
            'description' => 'Fantastic product',
        ]);

        $product1->prices()->create(['value' => 100]);
        $product1->prices()->create(['value' => 200]);
        $product1->prices()->create(['value' => 300]);

        $product2->prices()->create(['value' => 50]);
        $product2->prices()->create(['value' => 100]);
        $product2->prices()->create(['value' => 150]);

        $product3->prices()->create(['value' => 200]);
        $product3->prices()->create(['value' => 400]);
        $product3->prices()->create(['value' => 600]);
    }
}
