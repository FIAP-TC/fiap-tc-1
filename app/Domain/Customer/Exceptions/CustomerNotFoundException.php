<?php

namespace App\Domain\Customer\Exceptions;

use DomainException;

class CustomerNotFoundException extends DomainException
{
    public static function withId(int $id): self
    {
        return new self("Cliente com o ID {$id} não foi encontrado.");
    }
}