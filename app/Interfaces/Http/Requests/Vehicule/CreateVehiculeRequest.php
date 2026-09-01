<?php

namespace App\Interfaces\Http\Requests\Vehicule;

use Illuminate\Foundation\Http\FormRequest;

class CreateVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'plate'       => 'required|string|max:10|unique:vehicules,plate',
            'model'       => 'required|string|max:255',
            'brand'       => 'required|string|max:255',
            'years'       => 'required|integer|min:1900|max:2100',
            'status'      => 'sometimes|boolean',
            'customer_id' => 'required|integer|exists:customer,id',
        ];
    }
}
