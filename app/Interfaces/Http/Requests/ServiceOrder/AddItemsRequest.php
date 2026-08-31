<?php

namespace App\Interfaces\Http\Requests\ServiceOrder;

use Illuminate\Foundation\Http\FormRequest;

class AddItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'products'   => 'sometimes|array',
            'products.*' => 'integer|min:1',
            'services'   => 'sometimes|array',
            'services.*' => 'integer|min:1',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $hasProducts = !empty($this->input('products'));
            $hasServices = !empty($this->input('services'));

            if (!$hasProducts && !$hasServices) {
                $v->errors()->add('items', 'Informe ao menos um produto ou serviço.');
            }
        });
    }
}
