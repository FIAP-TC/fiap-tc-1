<?php

namespace App\Infrastructure\Persistence\Eloquent\ServiceOrder\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property string $status
 */
class ServiceOrderStatus extends Model
{
    protected $table = 'service_order_status';

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['id', 'name', 'status', 'create_date'];
}
