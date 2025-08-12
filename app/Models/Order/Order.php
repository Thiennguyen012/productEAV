<?php

namespace App\Models\Order;

use App\Models\Order\OrderItem;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'order';

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'note',
        'total',
        'shipping_fee',
        'tax_amount',
        'status',
        'payment_method'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helper method to get total items count
    public function getTotalItems()
    {
        return $this->orderItems->sum('quantity');
    }

    // Helper method to generate order number
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $lastOrder = self::whereDate('created_at', today())->latest()->first();
        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
