<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Eloquent da tabela 'role'.
 *
 * Responsabilidade: mapeamento da tabela de perfis de acesso para o ORM.
 * Regras de negócio relacionadas a roles pertencem à camada de Service/Entity.
 */
class Role extends Model
{
    use HasFactory;

    protected $table = 'role';

    // O schema usa create_date/modified_date em vez dos padrões created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
        'create_date',
        'modified_date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
