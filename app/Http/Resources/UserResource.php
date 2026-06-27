<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var User $user */
        $user = $this->resource;

        return [
            'id'       => $user->id,
            'username' => $user->username,
            'status'   => (bool) $user->status,
            'role'     => $this->whenLoaded('role', fn() => [
                'id'   => $user->role?->id,
                'name' => $user->role?->name,
            ]),
            'created_at' => $user->create_date,
            'updated_at' => $user->modified_date,
        ];
    }
}
