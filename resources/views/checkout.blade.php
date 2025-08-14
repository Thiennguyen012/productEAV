<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thanh toán - Ecommerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .checkout-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }
        .checkout-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
        }
        .btn-checkout {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary me-3">
                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>
                        <h2 class="mb-0">
                            <i class="fas fa-credit-card me-2 text-primary"></i>
                            Thanh toán
                        </h2>
                    </div>
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            @if($cart && count($cart) > 0)
            <form id="checkoutForm" action="{{ route('checkout.placeOrder') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Customer Information -->
                    <div class="col-lg-7">
                        <div class="checkout-card p-4 mb-4">
                            <h4 class="mb-4">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Thông tin khách hàng
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">
                                        Họ và tên <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="customer_name" 
                                           name="customer_name" required 
                                           placeholder="Nhập họ và tên">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">
                                        Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="customer_phone" 
                                           name="customer_phone" required 
                                           placeholder="Nhập số điện thoại">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="shipping_address" class="form-label">
                                    Địa chỉ giao hàng <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="shipping_address" 
                                          name="shipping_address" rows="3" required 
                                          placeholder="Nhập địa chỉ giao hàng chi tiết"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú đơn hàng</label>
                                <textarea class="form-control" id="note" name="note" rows="3" 
                                          placeholder="Ghi chú thêm cho đơn hàng (không bắt buộc)"></textarea>
                            </div>

                            <!-- Payment Method -->
                            <h5 class="mb-3">
                                <i class="fas fa-credit-card me-2 text-primary"></i>
                                Phương thức thanh toán
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="cod" value="offline" checked>
                                        <label class="form-check-label" for="cod">
                                            <i class="fas fa-money-bill-wave me-2"></i>
                                            Thanh toán khi nhận hàng (COD)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank_transfer" value="online">
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university me-2"></i>
                                            Chuyển khoản ngân hàng
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-5">
                        <div class="checkout-card p-4">
                            <h4 class="mb-4">
                                <i class="fas fa-shopping-bag me-2 text-primary"></i>
                                Tổng quan đơn hàng
                            </h4>

                            <!-- Cart Items -->
                            <div class="cart-items mb-4">
                                @php
                                    $subtotal = 0;
                                @endphp

                                @foreach($cart as $item)
                                    @php
                                        // Calculate item total using productVariant price if available
                                        $itemPrice = $item->productVariant ? $item->productVariant->price : 0;
                                        $itemTotal = $item->quantity * $itemPrice;
                                        $subtotal += $itemTotal;
                                    @endphp

                                    <div class="cart-item">
                                        <div class="d-flex align-items-center">
                                            @if($item->productVariant && $item->productVariant->product)
                                                <img src="{{ asset('images/' . $item->productVariant->product->image) }}" 
                                                     alt="{{ $item->productVariant->product->product_name }}"
                                                     class="product-image me-3">
                                            @else
                                                <div class="product-image me-3 bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif

                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    {{ $item->productVariant && $item->productVariant->product ? $item->productVariant->product->product_name : 'Sản phẩm không tồn tại' }}
                                                </h6>
                                                
                                                @if($item->productVariant)
                                                    <small class="text-muted d-block">SKU: {{ $item->productVariant->sku }}</small>
                                                    
                                                    @if($item->productVariant->options && $item->productVariant->options->count() > 0)
                                                        <small class="text-muted">
                                                            {{ $item->productVariant->product->product_name }}/{{ $item->productVariant->options->pluck('value')->join('/') }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">
                                                            {{ $item->productVariant->product->product_name }}
                                                        </small>
                                                    @endif
                                                @endif
                                                
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="text-muted">
                                                        {{ number_format($itemPrice) }}₫ × {{ $item->quantity }}
                                                    </span>
                                                    <strong class="text-primary">
                                                        {{ number_format($itemTotal) }}₫
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Summary -->
                            <div class="order-summary">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span>{{ number_format($subtotal) }}₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Tổng cộng:</strong>
                                    <strong class="text-primary fs-5">{{ number_format($subtotal) }}₫</strong>
                                </div>

                                <!-- Hidden total input for form submission -->
                                <input type="hidden" name="total" value="{{ $subtotal }}">

                                <!-- Checkout Button -->
                                <button type="submit" class="btn btn-checkout btn-primary w-100 mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đặt hàng
                                </button>

                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Thanh toán an toàn và bảo mật
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @else
            <!-- Empty Cart -->
            <div class="row">
                <div class="col-12">
                    <div class="checkout-card p-5 text-center">
                        <i class="fas fa-shopping-cart text-muted mb-3" style="font-size: 4rem;"></i>
                        <h4 class="mb-3">Giỏ hàng trống</h4>
                        <p class="text-muted mb-4">Bạn chưa có sản phẩm nào trong giỏ hàng để thanh toán.</p>
                        <a href="{{ route('products.showAll') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle checkout form submission
            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate form
                if (!this.checkValidity()) {
                    e.stopPropagation();
                    $(this).addClass('was-validated');
                    return;
                }

                // Disable submit button and show loading
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...'
                );

                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showAlert('success', response.message);
                            
                            // Redirect after 2 seconds
                            setTimeout(function() {
                                window.location.href = '/'; // Or order confirmation page
                            }, 2000);
                        } else {
                            showAlert('danger', response.message || 'Có lỗi xảy ra!');
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showAlert('danger', message);
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Phone number validation
            $('#customer_phone').on('input', function() {
                const phone = $(this).val().replace(/\D/g, '');
                $(this).val(phone);
            });

            // Show alert function
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                         style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                $('body').append(alertHtml);

                // Auto dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
</body>
</html>
