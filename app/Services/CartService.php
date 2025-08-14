<?php

namespace App\Services;

use App\Repositories\Interfaces\ICartItemRepo;
use App\Repositories\Interfaces\ICartRepo;
use App\Services\Interfaces\ICartService;
use Illuminate\Http\Request;

class CartService implements ICartService
{
    protected $cartRepo, $cartItemRepo;
    public function __construct(ICartRepo $cartRepo, ICartItemRepo $cartItemRepo)
    {
        $this->cartRepo = $cartRepo;
        $this->cartItemRepo = $cartItemRepo;
    }

    public function showCart($session_id)
    {
        $currCart = $this->cartRepo->findByCond(['session_id' => $session_id])->first();
        if (!$currCart) {
            return collect([]);
        }

        // Sử dụng Eloquent relationships để lấy cart items
        return $currCart->cartItems()->with(['productVariant.product', 'productVariant.options'])->get();
    }

    public function addToCart($session_id, Request $request)
    {
        //kiểm tra xem session_id đã có cart chưa
        $cart = $this->cartRepo->findByCond(['session_id' => $session_id])->first();

        if (!$cart) {
            // nếu chưa có cart với session_id hiện tại thì tạo mới cart
            $cart = $this->cartRepo->create(['session_id' => $session_id]);
            $cartItemData = [
                'cart_id' => $cart->id,
                'product_variant_id' => $request->input('product_variant_id'),
                'quantity' => $request->input('quantity'),
            ];
            return $this->cartItemRepo->create($cartItemData);
        } else {
            // kiểm tra xem variant đã có trong giỏ hàng chưa
            $existingItem = $this->cartItemRepo->findByCond([
                'cart_id' => $cart->id,
                'product_variant_id' => $request->input('product_variant_id'),
            ])->first();

            if ($existingItem) {
                // nếu variant đã có trong giỏ thì tăng số lượng
                $newQuantity = $existingItem->quantity + $request->input('quantity');
                return $this->cartItemRepo->update($existingItem->id, ['quantity' => $newQuantity]);
            } else {
                // nếu chưa có thì thêm mới
                $cartItemData = [
                    'cart_id' => $cart->id,
                    'product_variant_id' => $request->input('product_variant_id'),
                    'quantity' => $request->input('quantity'),
                ];
                return $this->cartItemRepo->create($cartItemData);
            }
        }
    }
    public function getCartCount($session_id)
    {
        $cart = $this->cartRepo->findByCond(['session_id' => $session_id])->first();
        if (!$cart) {
            return 0;
        }

        return $cart->cartItems()->sum('quantity');
    }
}
