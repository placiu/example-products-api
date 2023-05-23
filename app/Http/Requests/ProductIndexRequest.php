<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paginate' => ['nullable', 'integer', 'min:1'],
            'sortBy' => ['nullable', 'string', 'min:1', 'max:255'],
            'sortDirection' => ['nullable', Rule::in(['desc', 'asc', 'DESC', 'ASC'])],
            'name' => ['nullable', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'min:1', 'max:255'],
            'price-min' => ['nullable', 'integer', 'min:0'],
            'price-max' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
