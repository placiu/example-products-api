<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    const DEFAULT_PER_PAGE = 10;

    public function index(ProductIndexRequest $request)
    {
        $requestData = $request->validated();
        $products = Product::query()
            ->with('prices')
            ->when(isset($requestData['name']), fn($query) => $query->where('name', 'like', '%' . $requestData['name'] . '%'))
            ->when(isset($requestData['description']), fn($query) => $query->where('description', 'like', '%' . $requestData['description'] . '%'))
            ->orderBy($requestData['description'] ?? 'name', $requestData['description'] ?? 'asc')
            ->paginate(self::DEFAULT_PER_PAGE)
        ;

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
