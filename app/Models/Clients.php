<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;


/**
 * Model de Cliente
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property string      $phone
 * @property string      $birth_date
 * @property string      $address
 * @property string|null $complement
 * @property string      $neighborhood
 * @property string      $cep
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * @package App\Models
 */
class Clients extends Model
{
    use SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birth_date',
        'address',
        'complement',
        'neighborhood',
        'cep',
    ];

    protected $casts = [
        'id',
        'birth_date'=>'Timestamp',
        'updated_at'=>'Timestamp',
        'deleted_at'=>'Timestamp',
        'created_at'=>'Timestamp'
    ];

    protected $dates = [
        'birth_date',
    ];
}
