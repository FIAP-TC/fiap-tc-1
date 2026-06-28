<?php

namespace App\Providers;

use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VehiculeRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Repositories\VehiculeRepository;
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
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(VehiculeRepositoryInterface::class, VehiculeRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }
}
