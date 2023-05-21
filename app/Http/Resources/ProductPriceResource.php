<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductPriceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'value' => $this->value,
            'price' => $this->price(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
