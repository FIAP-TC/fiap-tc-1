<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource para serialização do usuário.
 *
 * Garante que a senha NUNCA seja retornada na resposta,
 * mesmo que o Model seja modificado no futuro.
 * Padroniza o formato de saída independente da estrutura interna do Model.
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'status'   => (bool) $this->status,
            'role'     => $this->whenLoaded('role', fn() => [
                'id'   => $this->role->id,
                'name' => $this->role->name,
            ]),
            'created_at' => $this->create_date,
            'updated_at' => $this->modified_date,
        ];
    }
}
