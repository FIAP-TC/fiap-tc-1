<?php

namespace App\Infrastructure\Persistence\Eloquent\Vehicule\Models;

use App\Infrastructure\Persistence\Eloquent\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int           $id
 * @property string        $name
 * @property string        $plate
 * @property string        $model
 * @property string        $brand
 * @property int           $years
 * @property bool          $status
 * @property int           $customer_id
 * @property \Illuminate\Support\Carbon      $create_date
 * @property \Illuminate\Support\Carbon|null $modified_date
 * @property Customer|null $customer
 */
class Vehicule extends Model
{
    use HasFactory;

    protected $table = 'vehicules';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'plate',
        'model',
        'brand',
        'years',
        'status',
        'customer_id',
        'create_date',
        'modified_date',
    ];

    protected $casts = [
        'status' => 'boolean',
        'create_date' => 'datetime',
        'modified_date' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
