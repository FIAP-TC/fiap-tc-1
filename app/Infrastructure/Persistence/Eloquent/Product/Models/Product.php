<?php

namespace App\Infrastructure\Persistence\Eloquent\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $type
 * @property float       $value
 * @property int         $quantity
 * @property bool        $status
 * @property string|null $create_date
 * @property string|null $modified_date
 */
class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'value',
        'quantity',
        'status',
        'create_date',
        'modified_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'value'  => 'float',
        'create_date' => 'datetime',
        'modified_date' => 'datetime',
    ];
}
