<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            // unique ignora o próprio usuário ao atualizar
            'username' => ['sometimes', 'string', 'max:255', "unique:users,username,{$userId}"],
            'password' => ['sometimes', 'string', 'min:6'],
            'role_id'  => ['sometimes', 'integer'],
            'status'   => ['sometimes', 'boolean'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors'  => $validator->errors(),
            'data'    => [],
        ], 422));
    }
}
