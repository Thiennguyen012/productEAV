<?php

namespace App\Services;

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
            return false; // Item không tồn tại
        }

        $newQuantity = $request->input('quantity', 1);

        // Validate quantity
        if ($newQuantity < 1) {
            return false; // Quantity không hợp lệ
        }

        return $item->update([
            'quantity' => $newQuantity,
        ]);
    }
    public function delteItem($itemId){
        return $this->cartItemRepo->find($itemId)->delete();
    }
}
