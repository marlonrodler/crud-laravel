<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    //
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'order_id',
        'quantity',
        'total_value',
    ];

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
