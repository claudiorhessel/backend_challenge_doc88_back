<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Product Model
 *
 * @property int         $id
 * @property string      $name
 * @property decimal     $price
 * @property string      $photo_original_name
 * @property string      $photo_name
 * @property string      $photo_destination_path
 * @property int         $type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'photo_original_name',
        'photo_name',
        'photo_destination_path',
        'type_id'
    ];

    protected $casts = [
        'id',
        'type_id',
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
     * Get the type record associated with the product.
     */
    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    /**
     * Get the order record associated with the product.
     */
    public function order()
    {
        return $this->belongsToMany('App\Models\Order')
                    ->using('App\Models\OrderProduct');
    }
}
