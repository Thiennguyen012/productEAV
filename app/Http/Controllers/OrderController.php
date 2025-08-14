<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ICartService;
use App\Services\Interfaces\IOrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderSevice, $cartService;

    public function __construct(IOrderService $orderSevice, ICartService $cartService)
    {
        $this->orderSevice = $orderSevice;
        $this->cartService = $cartService;
    }


    public function showOrder(Request $request)
    {
        $session_id = $this->getCurrSession($request);
        $cart = $this->cartService->showCart($session_id);
        return view('checkout', compact('cart'));
    }

    public function placeOrder(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_phone' => 'required|string|max:20',
                'shipping_address' => 'required|string|max:1000',
                'note' => 'nullable|string|max:1000',
                'total' => 'required|numeric|min:0',
                'payment_method' => 'required|in:online,offline',
            ]);

            $session_id = $this->getCurrSession($request);
            $result = $this->orderSevice->newOrder($session_id, $request);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công! Đơn hàng #' . $result->id . ' đã được tạo.',
                    'data' => $result,
                    'order_id' => $result->id
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Đặt hàng thất bại!'
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
