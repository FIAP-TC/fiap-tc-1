<?php

namespace App\Entities;

/**
 * Entidade de domínio para Role.
 *
 * Representa o conceito de perfil de acesso no domínio da aplicação,
 * desacoplado do Eloquent. Contém as constantes dos perfis disponíveis
 * e comportamentos de domínio relacionados a permissões.
 *
 * Padrão: Entities carregam identidade e comportamento de domínio.
 * Diferente de DTOs (que apenas transportam dados) e de Models (que
 * mapeiam o banco), Entities encapsulam regras do negócio.
 */
class RoleEntity
{
    // Nomes canônicos dos perfis cadastrados no seeder
    public const ADMINISTRADOR = 'Administrador';
    public const GERENTE       = 'Gerente';
    public const MECANICO      = 'Mecânico';

    // IDs fixos correspondentes aos seeders — evita magic numbers no código
    public const ID_ADMINISTRADOR = 1;
    public const ID_GERENTE       = 2;
    public const ID_MECANICO      = 3;

    public function __construct(
        private readonly int    $id,
        private readonly string $name,
        private readonly string $status,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === 'ativo';
    }

    /**
     * Verifica se este perfil tem permissão de administrador.
     * Administrador tem acesso total ao sistema.
     */
    public function isAdmin(): bool
    {
        return $this->id === self::ID_ADMINISTRADOR;
    }

    /**
     * Verifica se este perfil tem permissão de gerente ou superior.
     * Gerentes têm acesso a relatórios e aprovação de ordens.
     */
    public function isManagerOrAbove(): bool
    {
        return in_array($this->id, [self::ID_ADMINISTRADOR, self::ID_GERENTE], true);
    }
}
