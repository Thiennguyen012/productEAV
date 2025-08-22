<?php

namespace App\Services;

use App\Models\Product\ProductVariant;
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
        $productVariantId = $request->input('product_variant_id');
        $requestedQuantity = $request->input('quantity');

        // Kiểm tra biến thể sản phẩm có tồn tại không
        $productVariant = ProductVariant::find($productVariantId);
        if (!$productVariant) {
            return [
                'success' => false,
                'message' => 'Biến thể sản phẩm không tồn tại'
            ];
        }

        // Kiểm tra xem session_id đã có cart chưa
        $cart = $this->cartRepo->findByCond(['session_id' => $session_id])->first();

        if (!$cart) {
            // Kiểm tra tồn kho cho cart mới
            if ($requestedQuantity > $productVariant->quantity) {
                return [
                    'success' => false,
                    'message' => "Số lượng yêu cầu ({$requestedQuantity}) vượt quá tồn kho hiện có ({$productVariant->quantity})"
                ];
            }

            // Tạo cart mới và thêm item
            $cart = $this->cartRepo->create(['session_id' => $session_id]);
            $cartItemData = [
                'cart_id' => $cart->id,
                'product_variant_id' => $productVariantId,
                'quantity' => $requestedQuantity,
            ];
            $result = $this->cartItemRepo->create($cartItemData);

            return [
                'success' => true,
                'data' => $result,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công'
            ];
        } else {
            // Kiểm tra xem variant đã có trong giỏ hàng chưa
            $existingItem = $this->cartItemRepo->findByCond([
                'cart_id' => $cart->id,
                'product_variant_id' => $productVariantId,
            ])->first();

            if ($existingItem) {
                // Tính tổng số lượng sau khi cộng thêm
                $newQuantity = $existingItem->quantity + $requestedQuantity;

                // Kiểm tra tồn kho
                if ($newQuantity > $productVariant->quantity) {
                    return [
                        'success' => false,
                        'message' => "Số lượng sau khi cộng thêm ({$newQuantity}) vượt quá tồn kho hiện có ({$productVariant->quantity}). Hiện tại trong giỏ: {$existingItem->quantity}"
                    ];
                }

                $result = $this->cartItemRepo->update($existingItem->id, ['quantity' => $newQuantity]);

                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Cập nhật số lượng sản phẩm trong giỏ hàng thành công'
                ];
            } else {
                // Kiểm tra tồn kho cho item mới
                if ($requestedQuantity > $productVariant->quantity) {
                    return [
                        'success' => false,
                        'message' => "Số lượng yêu cầu ({$requestedQuantity}) vượt quá tồn kho hiện có ({$productVariant->quantity})"
                    ];
                }

                // Thêm item mới vào cart
                $cartItemData = [
                    'cart_id' => $cart->id,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $requestedQuantity,
                ];
                $result = $this->cartItemRepo->create($cartItemData);

                return [
                    'success' => true,
                    'data' => $result,
                    'message' => 'Thêm sản phẩm vào giỏ hàng thành công'
                ];
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
