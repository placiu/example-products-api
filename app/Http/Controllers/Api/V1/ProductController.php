<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('prices')->paginate(request('paginate') ?? 10);

        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request)
    {
        $productData = $request->validated();

        $product = Product::create($productData);

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $productData = $request->validated();

        $product->name = $productData['name'];
        $product->description = $productData['description'];
        $product->save();

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
