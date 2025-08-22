<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\ICartService;
use App\Services\Interfaces\IOrderService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    protected $orderSevice, $cartService;
    public function __construct(IOrderService $orderSevice, ICartService $cartService)
    {
        $this->orderSevice = $orderSevice;
        $this->cartService = $cartService;
    }

    public function getAll(Request $request)
    {
        $orderList = $this->orderSevice->getAllOrderWithItems($request);
        return view('Admin.order', compact('orderList'));
    }

    public function getOrderById($id)
    {
        try {
            $order = $this->orderSevice->getOrderWithItemsById($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng không tồn tại'
                ], 404);
            }

            return view('Admin.partials.order-details', compact('order'))->render();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải chi tiết đơn hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateOrder($orderId, Request $request)
    {
        // Validate input
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,shipping,delivered,cancelled'
        ]);

        try {
            $order = $this->orderSevice->updateOrderById($orderId, $request);

            if ($order) {
                $statusNames = [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao hàng',
                    'delivered' => 'Đã giao hàng',
                    'cancelled' => 'Đã hủy'
                ];

                $newStatus = $request->input('status');
                $statusText = $statusNames[$newStatus] ?? $newStatus;

                return response()->json([
                    'success' => true,
                    'message' => "Đã cập nhật trạng thái đơn hàng thành: {$statusText}",
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể cập nhật trạng thái đơn hàng',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }
}
