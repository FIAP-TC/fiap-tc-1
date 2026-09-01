<?php

namespace App\Providers;

use App\Domain\Customer\Repositories\CustomerRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use App\Domain\Service\Repositories\ServiceRepositoryInterface;
use App\Domain\ServiceOrder\Repositories\ServiceOrderRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Vehicule\Repositories\VehiculeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Customer\Repositories\CustomerRepository;
use App\Infrastructure\Persistence\Eloquent\Product\Repositories\ProductRepository;
use App\Infrastructure\Persistence\Eloquent\Service\Repositories\ServiceRepository;
use App\Infrastructure\Persistence\Eloquent\ServiceOrder\Repositories\ServiceOrderRepository;
use App\Infrastructure\Persistence\Eloquent\User\Repositories\UserRepository;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Repositories\VehicleRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider que vincula as interfaces de repositório às suas implementações.
 *
 * Este padrão (Dependency Inversion + IoC Container) permite:
 * 1. Os Services dependem da INTERFACE, não da implementação concreta.
 * 2. Em testes, basta registrar um mock no lugar da implementação real.
 * 3. Para trocar de ORM (ex: Doctrine), basta criar nova implementação e registrar aqui.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(VehiculeRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ServiceOrderRepositoryInterface::class, ServiceOrderRepository::class);
    }
}
