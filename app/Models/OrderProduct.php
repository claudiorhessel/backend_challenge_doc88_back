<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;


/**
 * Order Model
 *
 * @property int         $id
 * @property int         $order_id
 * @property int         $product_id
 * @property int         $product_qtd
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class OrderProduct extends Pivot
{
    protected $table = 'order_product';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_qtd'
    ];

    protected $casts = [
        'id',
        'order_id',
        'product_id',
        'updated_at'=>'datetime:Y-m-d H:i:s',
        'deleted_at'=>'datetime:Y-m-d H:i:s',
        'created_at'=>'datetime:Y-m-d H:i:s'
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
        return $this->belongsTo('App\Models\Order')->withTrashed();
    }

    /**
     * Get the product record associated with the order_products.
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product')->withTrashed();
    }
}
