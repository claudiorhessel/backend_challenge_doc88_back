<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Pastel Model
 *
 * @property int         $id
 * @property string      $name
 * @property decimal     $price
 * @property string      $photo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Pastel extends Model
{
    use SoftDeletes;

    protected $table = 'pastels';

    protected $fillable = [
        'name',
        'price',
        'photo'
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
}
