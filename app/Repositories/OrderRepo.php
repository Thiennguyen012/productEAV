<?php

namespace App\Repositories;

use App\Models\Order\Order;
use App\Models\Cart\Cart;
use App\Repositories\Interfaces\IOrderRepo;

class OrderRepo extends BaseRepo implements IOrderRepo
{
    /**
     * Create a new class instance.
     */
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    /**
     * Get cart by session ID with all related data
     * Ultra simplified - return the cart itself, let service handle the logic
     */

    public function getAllOrderWithItems($customerName = null, $status = null, $sort = null, $direction = null){
        
        $query = $this->model->with('orderItems');
        if($customerName){
            $query = $query->where('customer_name', 'like',"%$customerName%");
        }
        if($status){
            $query = $query->where('status', $status);
        }
        if($sort){
            $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
            switch($sort){
                case 'id':
                case 'customer_name':
                case 'update_at':
                case 'total':
                case 'payment_method':
                case 'status':
                    $query = $query->orderBy($sort, $direction);
                    break;
                default:
                    $query = $query->orderBy('id', $direction);
            }
        }
        else{
            $query = $query->orderBy('id','desc');
        }
        return $query->paginate(12);
    }

    public function getCartBySession($session_id)
    {
        return Cart::where('session_id', $session_id)
            ->with('cartItems.productVariant.product', 'cartItems.productVariant.options')
            ->first();
    }

    public function getCartItemsWithVariants($session_id)
    {
        $cart = $this->getCartBySession($session_id);

        return $cart?->cartItems
            ?->map(fn($item) => (object) [
                'id' => $item->id,
                'cart_id' => $item->cart_id,
                'product_variant_id' => $item->product_variant_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'price' => $item->unit_price,
                'product_id' => $item->productVariant->product_id,
                'variant_name' => $item->productVariant->options->pluck('value')->join(' / ')
                    ?: $item->productVariant->product->name ?? 'Default',
            ]) ?? collect([]);
    }
}
