<?php

namespace App\Http\Requests\OrderService;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => [
                'required',
                'integer',
                Rule::exists('service_order_status', 'id')
                    ->where('status', 1),
            ],
        ];
    }
}
