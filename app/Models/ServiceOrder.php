<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $users_id
 * @property int         $users_role_id
 * @property int         $vehicules_id
 * @property float       $order_value
 * @property float|null  $time_average
 * @property bool        $status
 * @property string      $create_date
 * @property string|null $modified_date
 * @property User|null   $user
 * @property Vehicule|null $vehicule
 */
class ServiceOrder extends Model
{
    use HasFactory;

    protected $table = 'service_order';

    public $timestamps = false;

    protected $fillable = [
        'users_id',
        'users_role_id',
        'vehicules_id',
        'order_value',
        'time_average',
        'status',
        'create_date',
        'modified_date',
    ];

    protected $casts = [
        'status'      => 'boolean',
        'order_value' => 'float',
        'time_average' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicules_id');
    }
}
