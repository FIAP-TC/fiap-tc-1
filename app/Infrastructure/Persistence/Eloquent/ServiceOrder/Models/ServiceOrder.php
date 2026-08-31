<?php

namespace App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models;

use App\Infrastructure\Persistence\Eloquent\Product\Models\Product;
use App\Infrastructure\Persistence\Eloquent\Service\Models\Service;
use App\Infrastructure\Persistence\Eloquent\Vehicule\Models\Vehicule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int                        $id
 * @property int                        $users_id
 * @property int                        $users_role_id
 * @property int                        $vehicules_id
 * @property float                      $order_value
 * @property float|null                 $time_average
 * @property bool                       $status
 * @property \Illuminate\Support\Carbon $create_date
 * @property \Illuminate\Support\Carbon|null $modified_date
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
        'status' => 'boolean',
        'order_value' => 'float',
        'time_average' => 'float',
        'create_date' => 'datetime',
        'modified_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function vehicule(): BelongsTo
    {
        return $this->belongsTo(Vehicule::class, 'vehicules_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'service_order_has_products',
            'service_order_id',
            'products_id'
        )->withPivot('charged_value');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(
            Service::class,
            'service_order_has_services',
            'service_order_id',
            'services_id'
        )->withPivot('charged_value');
    }

    public function statusHistory(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceOrderStatus::class,
            'service_order_has_service_order_status',
            'service_order_id',
            'service_order_status_id'
        )
            ->withPivot('create_date')
            ->orderBy('service_order_has_service_order_status.create_date');
    }
}
