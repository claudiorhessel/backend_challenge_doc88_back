<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Order Model
 *
 * @property int         $id
 * @property int         $order_id
 * @property int         $product_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class OrderProduct extends Model
{
    use SoftDeletes;

    protected $table = 'order_products';

    protected $fillable = [
        'order_id',
        'product_id'
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
     * Get the order record associated with the order_products.
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * Get the product record associated with the order_products.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * Get all of the posts for the country.
     */
    public function orderClient()
    {
        return $this->hasOneThrough(
            'App\Models\Client',
            'App\Models\Order',
            'id',
            'id',
            'id',
            'client_id'
        );
    }
}
