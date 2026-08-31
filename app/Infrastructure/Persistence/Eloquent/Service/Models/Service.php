<?php

namespace App\Infrastructure\Persistence\Eloquent\Service\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                        $id
 * @property string                     $name
 * @property float                      $value
 * @property bool                       $status
 * @property \Illuminate\Support\Carbon $create_date
 * @property \Illuminate\Support\Carbon|null $modified_date
 */
class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
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
