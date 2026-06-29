<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceOrderStatus extends Model
{
    protected $table = 'service_order_status';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status',
        'create_date',
        'modified_date',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
