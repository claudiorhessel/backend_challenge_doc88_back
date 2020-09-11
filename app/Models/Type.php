<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Type Model
 *
 * @property int         $id
 * @property string      $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Type extends Model
{
    use SoftDeletes;

    protected $table = 'types';

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'id',
        'updated_at'=>'Timestamp',
        'deleted_at'=>'Timestamp',
        'created_at'=>'Timestamp'
    ];

    protected $dates = [
        'updated_at'=>'Timestamp',
        'deleted_at'=>'Timestamp',
        'created_at'=>'Timestamp'
    ];

    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }
}
