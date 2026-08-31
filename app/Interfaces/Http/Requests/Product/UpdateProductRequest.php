<?php

namespace App\Interfaces\Http\Requests\Product;

use App\Domain\Product\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'filled', 'string', 'max:255'],
            'type' => [
                'sometimes',
                new Enum(ProductTypeEnum::class),
            ],
            'value' => ['sometimes', 'filled', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'filled', 'integer', 'min:0'],
            'status' => ['sometimes', 'filled', 'boolean'],
        ];
    }
}
