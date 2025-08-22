<?php

namespace App\Services;

use App\Repositories\Interfaces\ICartItemRepo;
use App\Repositories\Interfaces\IOrderItemRepo;
use App\Repositories\Interfaces\IOrderRepo;
use App\Services\Interfaces\IOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService implements IOrderService
{
    protected $orderRepo, $cartItemRepo, $orderItemRepo;
    public function __construct(IOrderRepo $orderRepo, ICartItemRepo $cartItemRepo, IOrderItemRepo $orderItemRepo)
    {
        $this->orderRepo = $orderRepo;
        $this->cartItemRepo = $cartItemRepo;
        $this->orderItemRepo = $orderItemRepo;
    }

    public function getAllOrderWithItems(Request $request)
    {
        $customerName = $request->input('customer_name');
        $status = $request->input('status');
        $sort = $request->input('sort');
        $direction = $request->input('direction');
        return $this->orderRepo->getAllOrderWithItems($customerName, $status, $sort, $direction);
    }

    public function newOrder($session_id, Request $request)
    {
        try {
            return DB::transaction(function () use ($session_id, $request) {
                // Tạo order từ request data
                $newOrder = $this->orderRepo->create([
                    'customer_name' => $request->input('customer_name'),
                    'customer_phone' => $request->input('customer_phone'),
                    'shipping_address' => $request->input('shipping_address'),
                    'note' => $request->input('note'),
                    'total' => $request->input('total'),
                    'payment_method' => $request->input('payment_method'),
                ]);

                // Kiểm tra order đã được tạo thành công
                if (!$newOrder) {
                    throw new Exception('Không thể tạo đơn hàng');
                }

                // Sử dụng relationship để lấy cart với tất cả dữ liệu liên quan
                $cart = $this->orderRepo->getCartBySession($session_id);

                if (!$cart) {
                    throw new Exception('Không tìm thấy giỏ hàng');
                }

                if ($cart->cartItems->isEmpty()) {
                    throw new Exception('Giỏ hàng trống');
                }

                // Tạo order items và kiểm tra inventory
                foreach ($cart->cartItems as $cartItem) {
                    // Kiểm tra variant vẫn còn tồn tại
                    if (!$cartItem->productVariant) {
                        throw new Exception('Sản phẩm không tồn tại trong hệ thống');
                    }

                    // Kiểm tra stock quantity (nếu có)
                    if ($cartItem->productVariant->quantity < $cartItem->quantity) {
                        throw new Exception('Số lượng sản phẩm ' . ($cartItem->productVariant->product->product_name ?? 'N/A') . ' không đủ');
                    }

                    // Tạo order item
                    $orderItem = $this->orderItemRepo->create([
                        'order_id' => $newOrder->id,
                        'product_id' => $cartItem->productVariant->product_id,
                        'product_variant_name' => $this->generateVariantName($cartItem->productVariant),
                        'price' => $cartItem->productVariant->price, // Sử dụng giá hiện tại của variant
                        'quantity' => $cartItem->quantity,
                    ]);

                    if (!$orderItem) {
                        throw new Exception('Không thể tạo chi tiết đơn hàng');
                    }

                    // Cập nhật inventory (giảm stock)
                    $newStock = $cartItem->productVariant->quantity - $cartItem->quantity;
                    $cartItem->productVariant->update(['quantity' => $newStock]);
                }

                // Xóa cart sau khi đặt hàng thành công
                $cart->cartItems()->delete(); // Xóa cart items
                $cart->delete(); // Xóa cart

                // Load order items để trả về đầy đủ thông tin
                $newOrder->load('orderItems');

                return $newOrder;
            });
        } catch (Exception $e) {
            // Log error nếu cần
            Log::error('Order creation failed: ' . $e->getMessage());

            // Throw lại exception để controller xử lý
            throw $e;
        }
    }

    /**
     * Generate variant name in format: product_name/option1/option2
     */
    private function generateVariantName($productVariant)
    {
        $productName = $productVariant->product->product_name ?? 'Unknown Product';

        if ($productVariant->options && $productVariant->options->count() > 0) {
            $options = $productVariant->options->pluck('value')->join('/');
            return $productName . '/' . $options;
        }

        return $productName;
    }

    public function getOrderWithItemsById($orderId)
    {
        return $this->orderRepo->getOrderWithItemsById($orderId);
    }

    public function updateOrderById($orderId, Request $request)
    {
        try {
            // Lấy order hiện tại để kiểm tra trạng thái
            $currentOrder = $this->orderRepo->find($orderId);

            if (!$currentOrder) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            $newStatus = $request->input('status');
            $currentStatus = $currentOrder->status ?? 'pending';

            // Kiểm tra tính hợp lệ của việc chuyển trạng thái
            if (!$this->isValidStatusTransition($currentStatus, $newStatus)) {
                throw new Exception($this->getInvalidTransitionMessage($currentStatus, $newStatus));
            }

            $arrayData = [
                'status' => $newStatus,
            ];

            return $this->orderRepo->update($orderId, $arrayData);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Kiểm tra xem có thể chuyển từ trạng thái hiện tại sang trạng thái mới không
     */
    private function isValidStatusTransition($currentStatus, $newStatus)
    {
        // Nếu trạng thái không thay đổi
        if ($currentStatus === $newStatus) {
            return false;
        }

        // Nếu đơn hàng đã hoàn thành hoặc đã hủy thì không thể thay đổi
        if (in_array($currentStatus, ['delivered', 'cancelled'])) {
            return false;
        }

        // Logic chuyển trạng thái hợp lệ
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping' => ['delivered', 'cancelled']
        ];

        return isset($validTransitions[$currentStatus]) &&
            in_array($newStatus, $validTransitions[$currentStatus]);
    }

    /**
     * Lấy thông báo lỗi khi chuyển trạng thái không hợp lệ
     */
    private function getInvalidTransitionMessage($currentStatus, $newStatus)
    {
        $statusNames = [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy'
        ];

        $currentName = $statusNames[$currentStatus] ?? $currentStatus;
        $newName = $statusNames[$newStatus] ?? $newStatus;

        if ($currentStatus === $newStatus) {
            return "Đơn hàng đã ở trạng thái {$currentName}";
        }

        if (in_array($currentStatus, ['delivered', 'cancelled'])) {
            return "Không thể thay đổi trạng thái đơn hàng đã {$currentName}";
        }

        return "Không thể chuyển từ trạng thái {$currentName} sang {$newName}";
    }
}
