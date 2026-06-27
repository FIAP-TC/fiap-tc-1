<?php

namespace Tests\Unit\Services;

use App\DTOs\User\UserDTO;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserService             $userService;
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->roleRepository = Mockery::mock(RoleRepositoryInterface::class);
        $this->userService    = new UserService($this->userRepository, $this->roleRepository);
    }

    public function test_list_all_retorna_colecao(): void
    {
        $this->userRepository->shouldReceive('findAll')->andReturn(new Collection());

        $result = $this->userService->listAll();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_find_by_id_existente_retorna_usuario(): void
    {
        $user = new User(['username' => 'admin']);
        $this->userRepository->shouldReceive('findById')->with(1)->andReturn($user);

        $result = $this->userService->findById(1);

        $this->assertEquals('admin', $result->username);
    }

    public function test_find_by_id_inexistente_lanca_excecao(): void
    {
        $this->userRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->userService->findById(99);
    }

    public function test_create_com_role_valida_cria_usuario(): void
    {
        $role        = new Role();
        $role->id    = 1;

        $createdUser     = new User();
        $createdUser->id = 10; // atribuição direta ignora o $fillable — necessário para unit tests

        $freshUser = new User();
        $freshUser->setAttribute('username', 'novo');
        $freshUser->setRelation('role', $role);

        $this->roleRepository->shouldReceive('findById')->with(1)->andReturn($role);
        $this->userRepository->shouldReceive('create')->andReturn($createdUser);
        // O Service chama findById após o create para retornar o user com a role carregada
        $this->userRepository->shouldReceive('findById')->with(10)->andReturn($freshUser);

        $dto    = new UserDTO('novo', 'senha123', 1);
        $result = $this->userService->create($dto);

        $this->assertEquals('novo', $result->username);
    }

    public function test_create_com_role_invalida_lanca_excecao(): void
    {
        $this->roleRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(422);

        $this->userService->create(new UserDTO('novo', 'senha123', 99));
    }

    public function test_delete_usuario_existente(): void
    {
        $user = new User(['id' => 1]);
        $this->userRepository->shouldReceive('findById')->with(1)->andReturn($user);
        $this->userRepository->shouldReceive('delete')->with(1)->andReturn(true);

        // Não deve lançar exceção
        $this->userService->delete(1);
        $this->assertTrue(true);
    }

    public function test_delete_usuario_inexistente_lanca_excecao(): void
    {
        $this->userRepository->shouldReceive('findById')->with(99)->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionCode(404);

        $this->userService->delete(99);
    }

    public function test_update_com_nova_role_valida(): void
    {
        $existingUser = new User(['id' => 1, 'username' => 'admin']);
        $updatedUser  = new User(['id' => 1, 'username' => 'admin_new']);
        $role         = new Role(['id' => 2]);

        $this->userRepository->shouldReceive('findById')->with(1)->andReturn($existingUser, $updatedUser);
        $this->roleRepository->shouldReceive('findById')->with(2)->andReturn($role);
        $this->userRepository->shouldReceive('update')->andReturn(true);

        $dto    = new UserDTO(username: 'admin_new', roleId: 2);
        $result = $this->userService->update(1, $dto);

        $this->assertEquals('admin_new', $result->username);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
