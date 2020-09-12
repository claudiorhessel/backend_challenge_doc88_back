<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Order Model
 *
 * @property int         $id
 * @property int         $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'client_id'
    ];

    protected $casts = [
        'id',
        'client_id',
        'updated_at'=>'Timestamp',
        'deleted_at'=>'Timestamp',
        'created_at'=>'Timestamp'
    ];

    protected $dates = [
        'updated_at'=>'Timestamp',
        'deleted_at'=>'Timestamp',
        'created_at'=>'Timestamp'
    ];

    /**
     * Get the type record associated with the product.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the type record associated with the product.
     */
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get all of the products for the order.
     */
    public function productOrder()
    {
        return $this->hasManyThrough(
            'App\Models\Product',
            'App\Models\OrderProduct',
            'order_id',
            'id',
            'id',
            'product_id'
        );
    }
}
