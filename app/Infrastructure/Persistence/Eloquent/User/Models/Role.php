<?php

namespace App\Infrastructure\Persistence\Eloquent\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $status
 * @property string|null $create_date
 * @property string|null $modified_date
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
