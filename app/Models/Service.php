<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int         $id
 * @property string      $name
 * @property float       $value
 * @property bool        $status
 * @property string      $create_date
 * @property string|null $modified_date
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
    ];
}
