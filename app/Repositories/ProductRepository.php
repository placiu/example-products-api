<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    const DEFAULT_PER_PAGE = 10;

    public function filterAll(array $filters): LengthAwarePaginator
    {
        // DB::enableQueryLog();

        $result = Product::query()
            ->whereHas('prices', function ($query) use ($filters) {
                $query->when(isset($filters['price-min']), function($query) use ($filters) { 
                    $query->where('value', '>=', $filters['price-min']);
                });
                $query->when(isset($filters['price-max']), function($query) use ($filters) { 
                    $query->where('value', '<=', $filters['price-max']);
                });
            })
            ->when(isset($filters['name']), function($query) use ($filters) {
                $query->where('name', 'like', '%' . $filters['name'] . '%');
            })
            ->when(isset($filters['description']), function($query) use ($filters) { 
                $query->where('description', 'like', '%' . $filters['description'] . '%'); 
            })
            ->orderBy($filters['sortBy'] ?? 'name', $filters['sortDirection'] ?? 'asc')
            ->paginate($filters['paginate'] ?? self::DEFAULT_PER_PAGE);

        // $queryLog = DB::getQueryLog();
        // $lastQuery = end($queryLog);
        // dd($lastQuery);

        return $result;
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->name = $data['name'];
        $product->description = $data['description'];
        $product->save();

        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }
}