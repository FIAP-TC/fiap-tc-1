<?php

namespace App\DTOs\ServiceOrder;

/**
 * DTO de criação de Ordem de Serviço.
 *
 * Os campos users_id e users_role_id são injetados pelo Controller a partir
 * do token JWT autenticado — o cliente não os envia no body da requisição.
 */
class ServiceOrderDTO
{
    public function __construct(
        public readonly int    $usersId,
        public readonly int    $usersRoleId,
        public readonly int    $vehiculesId,
        public readonly ?float $timeAverage = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            usersId:      (int) $data['users_id'],
            usersRoleId:  (int) $data['users_role_id'],
            vehiculesId:  (int) $data['vehicules_id'],
            timeAverage:  isset($data['time_average']) ? (float) $data['time_average'] : null,
        );
    }
}
