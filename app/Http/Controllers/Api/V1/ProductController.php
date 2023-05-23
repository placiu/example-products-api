<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function __construct(
        public ProductRepository $repository
    ) {}

    public function index(ProductIndexRequest $request)
    {
        $filters = $request->validated();
        
        $products = $this->repository->filterAll($filters);

        return ProductResource::collection($products);
    }

    public function store(ProductStoreRequest $request)
    {
        $productData = $request->validated();

        $product = $this->repository->create($productData);

        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $productData = $request->validated();

        $product = $this->repository->update($product, $productData);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $this->repository->delete($product);

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
