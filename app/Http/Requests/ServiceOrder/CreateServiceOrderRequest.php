<?php

namespace App\Http\Requests\ServiceOrder;

use Illuminate\Foundation\Http\FormRequest;

class CreateServiceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicules_id' => 'required|integer|exists:vehicules,id',
            'time_average' => 'sometimes|numeric|min:0',
        ];
    }
}
