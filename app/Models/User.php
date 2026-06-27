<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Model Eloquent da tabela 'users'.
 *
 * Responsabilidade: mapeamento da tabela de usuários e implementação
 * da interface JWTSubject, necessária para o tymon/jwt-auth gerar
 * e validar tokens a partir desta entidade.
 *
 * O campo de autenticação é 'username' (não 'email') conforme schema do projeto.
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;

    // O schema usa create_date/modified_date em vez dos padrões created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'status',
        'role_id',
        'create_date',
        'modified_date',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Contratos JWTSubject
    // O JWT precisa de um identificador único do subject (sub) e de claims extras.
    // -------------------------------------------------------------------------

    /**
     * Retorna a chave usada como subject (sub) do token JWT.
     * Usamos o id primário do usuário para garantir unicidade.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Claims customizados adicionados ao payload do JWT.
     * Incluímos role_id para que o middleware de role não precise
     * fazer uma query extra no banco a cada requisição.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role_id' => $this->role_id,
        ];
    }

    // -------------------------------------------------------------------------
    // Relacionamentos
    // -------------------------------------------------------------------------

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
