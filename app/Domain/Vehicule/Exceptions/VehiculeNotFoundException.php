<?php

namespace App\Domain\Vehicule\Exceptions;

use DomainException;

class VehiculeNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Veiculo com o ID {$id} não foi encontrado.");
    }
}