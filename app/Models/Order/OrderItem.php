<?php

namespace App\Models\Order;

use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Models\Product\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_item';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_variant_name',
        'price',
        'quantity'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
