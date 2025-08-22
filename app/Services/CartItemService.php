<?php

namespace App\Services;

use App\Models\Product\ProductVariant;
use App\Repositories\Interfaces\ICartItemRepo;
use App\Services\Interfaces\ICartItemService;
use Illuminate\Http\Request;

class CartItemService implements ICartItemService
{
    protected $cartItemRepo;
    public function __construct(ICartItemRepo $cartItemRepo)
    {
        $this->cartItemRepo = $cartItemRepo;
    }

    public function updateItem($itemId, Request $request)
    {
        $item = $this->cartItemRepo->find($itemId);

        if (!$item) {
            return [
                'success' => false,
                'message' => 'Item không tồn tại trong giỏ hàng'
            ];
        }

        $newQuantity = $request->input('quantity', 1);

        // Validate quantity
        if ($newQuantity < 1) {
            return [
                'success' => false,
                'message' => 'Số lượng phải lớn hơn 0'
            ];
        }

        // Kiểm tra tồn kho
        $productVariant = ProductVariant::find($item->product_variant_id);
        if (!$productVariant) {
            return [
                'success' => false,
                'message' => 'Biến thể sản phẩm không tồn tại'
            ];
        }

        if ($newQuantity > $productVariant->quantity) {
            return [
                'success' => false,
                'message' => "Số lượng yêu cầu ({$newQuantity}) vượt quá tồn kho hiện có ({$productVariant->quantity})"
            ];
        }

        $result = $item->update([
            'quantity' => $newQuantity,
        ]);

        if ($result) {
            return [
                'success' => true,
                'message' => 'Cập nhật số lượng thành công'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật số lượng'
            ];
        }
    }
    public function delteItem($itemId)
    {
        return $this->cartItemRepo->find($itemId)->delete();
    }
}
