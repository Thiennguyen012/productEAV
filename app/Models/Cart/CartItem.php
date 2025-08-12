<?php

namespace App\Models\Cart;

use App\Models\Cart\Cart;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_item';

    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
        'unit_price'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
