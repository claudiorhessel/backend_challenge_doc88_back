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
class Client extends Model
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
        'birth_date'=>'date:Y-m-d',
        'updated_at'=>'datetime:Y-m-d H:i:s',
        'deleted_at'=>'datetime:Y-m-d H:i:s',
        'created_at'=>'datetime:Y-m-d H:i:s'
    ];

    protected $dates = [
        'birth_date',
    ];

    /**
     * Get the order record associated with the client.
     */
    public function order()
    {
        return $this->hasMany('App\Models\Order');
    }
}
