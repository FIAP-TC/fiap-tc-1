<?php

namespace App\Infrastructure\Persistence\Eloquent\Customer\Models;

use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $identification
 * @property int         $identification_number
 * @property string      $email
 * @property bool        $status
 * @property \Illuminate\Support\Carbon|null $create_date
 * @property \Illuminate\Support\Carbon|null $modified_date
 */
class Customer extends Model
{
    use HasFactory;

    protected $table = 'customer';

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'modified_date';

    public $timestamps = false;

    protected $attributes = [
        'status' => 1,
    ];

    protected $fillable = [
        'name',
        'identification',
        'identification_number',
        'email',
        'status',
        'create_date',
        'modified_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'create_date' => 'datetime',
        'modified_date' => 'datetime',
    ];

    public function vehicules(): HasMany
    {
        return $this->hasMany(Vehicule::class, 'customer_id');
    }
}
