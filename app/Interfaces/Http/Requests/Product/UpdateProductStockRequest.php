<?php

namespace App\Interfaces\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductStockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }
}
