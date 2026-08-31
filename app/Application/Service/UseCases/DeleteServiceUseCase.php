<?php

namespace App\Application\Service\UseCases;

use App\Domain\Service\Exceptions\ServiceNotFoundException;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;

final class DeleteServiceUseCase
{
    public function __construct(
        private readonly ServiceRepositoryInterface $serviceRepository,
    ) {}

    public function execute(int $id): bool
    {
        $deleted = $this->serviceRepository->delete($id);

        if (!$deleted) {
            throw ServiceNotFoundException::withId($id);
        }

        return true;
    }
}
