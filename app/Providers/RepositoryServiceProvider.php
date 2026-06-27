<?php

namespace App\Providers;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
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
    }
}
