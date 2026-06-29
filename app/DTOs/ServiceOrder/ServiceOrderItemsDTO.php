<?php

namespace App\DTOs\ServiceOrder;

/**
 * DTO para associação de Produtos e Serviços a uma Ordem de Serviço.
 *
 * Ambas as listas são opcionais — permite enviar só produtos, só serviços
 * ou ambos na mesma requisição. O Service recalcula o valor total após
 * qualquer inserção.
 */
class ServiceOrderItemsDTO
{
    public function __construct(
        public readonly array $productIds = [],
        public readonly array $serviceIds = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            productIds: array_map('intval', $data['products'] ?? []),
            serviceIds: array_map('intval', $data['services'] ?? []),
        );
    }
}
