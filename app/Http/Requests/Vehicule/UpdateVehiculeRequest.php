<?php

namespace App\Http\Requests\Vehicule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehiculeId = $this->route('id');

        return [
            'name'        => 'sometimes|string|max:255',
            'plate'       => "sometimes|string|max:10|unique:vehicules,plate,{$vehiculeId}",
            'model'       => 'sometimes|string|max:255',
            'brand'       => 'sometimes|string|max:255',
            'years'       => 'sometimes|integer|min:1900|max:2100',
            'status'      => 'sometimes|boolean',
            'customer_id' => 'sometimes|integer|exists:customer,id',
        ];
    }
}
