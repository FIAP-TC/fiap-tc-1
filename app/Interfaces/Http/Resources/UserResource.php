<?php

namespace App\Interfaces\Http\Resources;

use App\Domain\User\Entites\UserEntity;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read UserEntity $resource
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var UserEntity $user */
        $user = $this->resource;

        return [
            'id'         => $user->getId(),
            'username'   => $user->getUsername(),
            'status'     => $user->isActive(),
            'role'       => $user->getRole() ? [
                'id'   => $user->getRole()->getId(),
                'name' => $user->getRole()->getName(),
            ] : null,
            'created_at' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $user->getModifiedDate()?->format('Y-m-d H:i:s'),
        ];
    }
}
