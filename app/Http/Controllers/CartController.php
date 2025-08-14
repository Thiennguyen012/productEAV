<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ICartItemService;
use App\Services\Interfaces\ICartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;
    protected $cartItemService;
    public function __construct(ICartService $cartService, ICartItemService $cartItemService)
    {
        $this->cartService = $cartService;
        $this->cartItemService = $cartItemService;
    }

    public function showCart(Request $request)
    {
        $session_id = $this->getCurrSession($request);

        $cart = $this->cartService->showCart($session_id);
        return view('cart', compact('cart'));
    }
    public function addToCart(Request $request)
    {
        // Tạo hoặc lấy session cart ID
        if (!$request->session()->has('cart_session_id')) {
            // Nếu chưa có session cart, tạo mới
            $session_id = 'cart_' . uniqid() . '_' . time();
            $request->session()->put('cart_session_id', $session_id);
        } else {
            // Lấy session cart ID đã có
            $session_id = $request->session()->get('cart_session_id');
        }

        $result = $this->cartService->addToCart($session_id, $request);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Thêm sản phẩm vào giỏ hàng thành công !',
                'cart_count' => $this->cartService->getCartCount($session_id),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm sản phẩm vào giỏ hàng',
            ], 400);
        }
    }
    public function updateItem($itemId, Request $request)
    {
        $result = $this->cartItemService->updateItem($itemId, $request);
        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật giỏ hàng thất bại!'
            ], 400);
        }
    }
    public function deleteCartItem($itemId){
        $result = $this->cartItemService->delteItem($itemId);
        if($result){
            return response()->json([
                'success' => true,
                'message' => 'Xóa sản phẩm khỏi giỏ hàng thành công!',
            ]);
        }
        else{
            return response()->json([
                'success' => false,
                'message' => 'Xóa sản phẩm khỏi giỏ hàng thất bại!'
            ]);
        }
    }
}
