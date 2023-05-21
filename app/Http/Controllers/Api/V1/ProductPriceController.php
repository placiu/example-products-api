<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\ProductPrice;
use Illuminate\Http\Response;

class ProductPriceController extends Controller
{
    public function index()
    {
        $prices = ProductPrice::paginate(request('paginate') ?? 10);

        return ProductPriceResource::collection($prices);
    }

    public function store(ProductPriceRequest $request)
    {
        $productPriceData = $request->validated();

        $price = ProductPrice::create($productPriceData);

        return new ProductPriceResource($price);
    }

    public function show(ProductPrice $productPrice)
    {
        return new ProductPriceResource($productPrice);
    }

    public function update(ProductPriceRequest $request, ProductPrice $productPrice)
    {
        $productPriceData = $request->validated();

        $productPrice->value = $productPriceData['value'];
        $productPrice->save();

        return new ProductPriceResource($productPrice);
    }

    public function destroy(ProductPrice $productPrice)
    {
        $productPrice->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
