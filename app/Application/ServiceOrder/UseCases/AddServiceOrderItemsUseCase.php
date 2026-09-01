<?php

namespace App\Application\ServiceOrder\UseCases;

use App\Application\ServiceOrder\DTOs\ServiceOrderItemsDTO;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\Domain\ServiceOrder\Entites\ServiceOrderEntity;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderItemsNotFoundException;
use App\Domain\ServiceOrder\Exceptions\ServiceOrderNotFoundException;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Adiciona produtos e/ou serviços a uma ordem existente e recalcula o total.
 *
 * Regras:
 * - A ordem deve existir (ativa ou inativa — permite adicionar antes de reativar).
 * - Todos os IDs de produtos e serviços devem existir.
 * - Duplicatas são ignoradas (insertOrIgnore) — o mesmo item não é cobrado duas vezes.
 * - O valor total é recalculado APÓS a inserção, considerando todos os itens acumulados.
 */
final class AddServiceOrderItemsUseCase
{
    public function __construct(
        private readonly ServiceOrderRepositoryInterface $serviceOrderRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ServiceRepositoryInterface $serviceRepository,
        private readonly VehiculeRepositoryInterface $vehiculeRepository,
    ) {}

    public function execute(int $orderId, ServiceOrderItemsDTO $dto): ServiceOrderEntity
    {
        $order = $this->serviceOrderRepository->findByIdIgnoringStatus($orderId);

        if (!$order) {
            throw ServiceOrderNotFoundException::withId($orderId);
        }

        $products = $this->validateProducts($dto->productIds);
        $services = $this->validateServices($dto->serviceIds);

        $vehicule = $this->vehiculeRepository->findByIdIgnoringStatus($order->getVehiculesId());
        $customerId = $vehicule?->getCustomerId() ?? 0;

        return DB::transaction(function () use ($order, $products, $services, $customerId) {
            if (!empty($products)) {
                $this->serviceOrderRepository->addProducts(
                    $order->getId(),
                    $customerId,
                    $order->getUsersId(),
                    $order->getUsersRoleId(),
                    $products,
                );
            }

            if (!empty($services)) {
                $this->serviceOrderRepository->addServices(
                    $order->getId(),
                    $customerId,
                    $order->getUsersId(),
                    $order->getUsersRoleId(),
                    $services,
                );
            }

            $total = $this->serviceOrderRepository->calculateOrderTotal($order->getId());
            $this->serviceOrderRepository->updateOrderValue($order->getId(), $total);

            return $this->serviceOrderRepository->findByIdIgnoringStatus($order->getId());
        });
    }

    private function validateProducts(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $found = $this->productRepository->findManyByIds($ids);
        $missing = array_diff($ids, array_map(fn ($product) => $product->getId(), $found));

        if (!empty($missing)) {
            throw ServiceOrderItemsNotFoundException::forProducts($missing);
        }

        return $found;
    }

    private function validateServices(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $found = $this->serviceRepository->findManyByIds($ids);
        $missing = array_diff($ids, array_map(fn ($service) => $service->getId(), $found));

        if (!empty($missing)) {
            throw ServiceOrderItemsNotFoundException::forServices($missing);
        }

        return $found;
    }
}
