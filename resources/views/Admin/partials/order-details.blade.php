@if($order)
<div class="order-details-container">
    <!-- Order Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted mb-1">Mã đơn hàng</h6>
            <h5 class="fw-bold">#{{ $order->id }}</h5>
        </div>
        <div class="col-md-6 text-md-end">
            <h6 class="text-muted mb-1">Ngày đặt</h6>
            <p class="mb-0">{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
        </div>
    </div>

    <!-- Order Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-light">
                <div class="card-body text-center py-3">
                    <span class="order-status 
                        @switch($order->status ?? 'pending')
                            @case('pending') status-pending @break
                            @case('confirmed') status-confirmed @break
                            @case('shipping') status-shipping @break
                            @case('delivered') status-delivered @break
                            @case('cancelled') status-cancelled @break
                            @default status-pending
                        @endswitch
                    ">
                        {{ match($order->status ?? 'pending') {
                            'pending' => 'Chờ xử lý',
                            'confirmed' => 'Đã xác nhận', 
                            'shipping' => 'Đang giao',
                            'delivered' => 'Đã giao',
                            'cancelled' => 'Đã hủy',
                            default => 'Chờ xử lý'
                        } }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Thông tin khách hàng
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Tên:</strong> {{ $order->customer_name ?? 'N/A' }}
                    </p>
                    <p class="mb-2">
                        <strong>Điện thoại:</strong> {{ $order->customer_phone ?? 'N/A' }}
                    </p>
                    <p class="mb-0">
                        <strong>Địa chỉ:</strong> {{ $order->shipping_address ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>
                        Thông tin thanh toán
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Phương thức:</strong> 
                        {{ match($order->payment_method ?? 'cod') {
                            'cod' => 'Thanh toán khi nhận hàng',
                            'bank' => 'Chuyển khoản ngân hàng',
                            'momo' => 'Ví MoMo',
                            'vnpay' => 'VNPay',
                            default => 'Thanh toán khi nhận hàng'
                        } }}
                    </p>
                    <p class="mb-2">
                        <strong>Tổng tiền:</strong> 
                        <span class="text-success fw-bold">
                            {{ number_format($order->total ?? 0, 0, ',', '.') }}₫
                        </span>
                    </p>
                    @if($order->note)
                        <p class="mb-0">
                            <strong>Ghi chú:</strong> {{ $order->note }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-box me-2"></i>
                Chi tiết sản phẩm
                @if($order->orderItems && $order->orderItems->count() > 0)
                    <span class="badge bg-primary ms-2">{{ $order->orderItems->count() }} sản phẩm</span>
                @endif
            </h6>
        </div>
        <div class="card-body p-0">
            @if($order->orderItems && $order->orderItems->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Đơn giá</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('images/' . $item->product->image) }}" 
                                                     alt="{{ $item->product_variant_name ?? 'Product' }}"
                                                     class="rounded me-3"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $item->product_variant_name ?? 'N/A' }}</h6>
                                                @if($item->product)
                                                    <small class="text-muted">{{ $item->product->product_name }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ number_format($item->price ?? 0, 0, ',', '.') }}₫
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark">{{ $item->quantity ?? 0 }}</span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 0, ',', '.') }}₫
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Tổng cộng:</th>
                                <th class="text-end text-success">
                                    {{ number_format($order->total ?? 0, 0, ',', '.') }}₫
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                    <h6 class="text-muted">Không có sản phẩm trong đơn hàng</h6>
                </div>
            @endif
        </div>
    </div>

    <!-- Order Timeline (if status history exists) -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Lịch sử đơn hàng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Đơn hàng được tạo</h6>
                                <small class="text-muted">
                                    {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                        
                        @if($order->updated_at && $order->updated_at != $order->created_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-info"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Cập nhật gần nhất</h6>
                                    <small class="text-muted">
                                        {{ $order->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.order-status {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-confirmed {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-shipping {
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #99d6ff;
}

.status-delivered {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -23px;
    top: 20px;
    height: calc(100% + 10px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content h6 {
    color: #495057;
    margin-bottom: 4px;
}
</style>

@else
<div class="text-center py-5">
    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
    <h5>Không tìm thấy đơn hàng</h5>
    <p class="text-muted">Đơn hàng có thể đã bị xóa hoặc không tồn tại.</p>
</div>
@endif
