<?php

namespace App\Models\Cart;

use App\Models\Cart\CartItem;
use App\Models\Cart\CartSession;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    protected $fillable = [
        'session_id'
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function cartSession()
    {
        return $this->belongsTo(CartSession::class, 'session_id', 'session_id');
    }

    // Helper method to get total amount
    public function getTotalAmount()
    {
        return $this->cartItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    // Helper method to get total items count
    public function getTotalItems()
    {
        return $this->cartItems->sum('quantity');
    }

    // Helper method to add item to cart
    public function addItem($productVariantId, $quantity, $unitPrice)
    {
        $existingItem = $this->cartItems()->where('product_variant_id', $productVariantId)->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity
            ]);
            return $existingItem;
        }

        return $this->cartItems()->create([
            'product_variant_id' => $productVariantId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice
        ]);
    }

    // Helper method to remove item from cart
    public function removeItem($productVariantId)
    {
        return $this->cartItems()->where('product_variant_id', $productVariantId)->delete();
    }

    // Helper method to clear cart
    public function clearCart()
    {
        return $this->cartItems()->delete();
    }
}
