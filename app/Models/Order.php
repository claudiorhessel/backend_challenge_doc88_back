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
     * Get the client record associated with the order.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the order_products record associated with the order.
     */
    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the product record associated with the order.
     */
    public function product()
    {
        return $this->belongsToMany('App\Models\Product')
                    ->using('App\Models\OrderProduct')
                    ->withPivot([
                        'product_qtd',
                    ]);
    }
}
