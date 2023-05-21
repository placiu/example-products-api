<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'ulid'],
            'value' => ['required', 'integer', 'min:0'],
            'precision' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
