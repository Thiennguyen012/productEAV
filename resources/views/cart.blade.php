<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giỏ hàng - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        .cart-item {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            margin-bottom: 1rem;
            padding: 1.5rem;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .cart-item:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-control {
            border: 1px solid #ddd;
            border-radius: 25px;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
        }
        .quantity-control button {
            border: none;
            background: #f8f9fa;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .quantity-control button:hover {
            background: #e9ecef;
        }
        .quantity-control input {
            border: none;
            text-align: center;
            width: 60px;
            background: white;
        }
        .price-text {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .total-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            position: sticky;
            top: 20px;
        }
        .checkout-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            color: white;
            font-weight: bold;
            transition: all 0.3s;
            width: 100%;
        }
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .empty-cart {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        .variant-info {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header -->
    <div class="cart-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-shopping-cart me-3"></i>
                        Giỏ hàng của bạn
                    </h1>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('products.showAll') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        @if($cart && $cart->count() > 0)
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4>Sản phẩm trong giỏ ({{ $cart->count() }} sản phẩm)</h4>
                        <button class="btn btn-outline-secondary btn-sm" onclick="clearCart()">
                            <i class="fas fa-trash me-1"></i>
                            Xóa tất cả
                        </button>
                    </div>

                    <div id="cartItemsContainer">
                        @foreach($cart as $item)
    <div class="cart-item mb-3 p-3 border rounded" data-item-id="{{ $item->id }}">
        <div class="row align-items-center g-3">
            <!-- Product Image -->
            <div class="col-12 col-md-2">
                @if($item->productVariant && $item->productVariant->product)
                    <div class="text-center">
                        <img src="{{ asset('images/' . $item->productVariant->product->image) }}" 
                             alt="{{ $item->productVariant->product->product_name }}"
                             class="product-image img-fluid rounded"
                             style="max-width: 80px; max-height: 80px; object-fit: cover;">
                    </div>
                @else
                    <div class="product-image bg-secondary d-flex align-items-center justify-content-center rounded mx-auto"
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-image text-white"></i>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="col-12 col-md-4">
                @if($item->productVariant && $item->productVariant->product)
                    <div class="product-details">
                        <h6 class="mb-2 fw-bold">{{ $item->productVariant->product->product_name }}</h6>
                        <p class="text-muted mb-2 small">
                            <strong>SKU:</strong> {{ $item->productVariant->sku }}
                        </p>
                        
                        @if($item->productVariant->options && $item->productVariant->options->count() > 0)
                            <div class="variant-info mb-2">
                                <small class="text-muted d-block mb-1">Phiên bản:</small>
                                @foreach($item->productVariant->options as $option)
                                    <span class="badge bg-light text-dark me-1 border">{{ $option->value }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="product-details">
                        <h6 class="mb-1 text-muted">Sản phẩm không tồn tại</h6>
                    </div>
                @endif
            </div>

            <!-- Price per unit -->
            <div class="col-6 col-md-2">
                <div class="text-center">
                    <small class="text-muted d-block">Đơn giá</small>
                    @if($item->productVariant)
                        <div class="price-text fw-bold text-primary">
                            {{ number_format($item->productVariant->price, 0, ',', '.') }}₫
                        </div>
                    @else
                        <div class="text-muted">N/A</div>
                    @endif
                </div>
            </div>

            <!-- Quantity Control -->
            <div class="col-6 col-md-2">
                <div class="text-center">
                    <small class="text-muted d-block mb-2">Số lượng</small>
                    <div class="quantity-control d-flex align-items-center justify-content-center">
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm"
                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                            <i class="fas fa-minus"></i>
                        </button>
                        
                        <input type="number" 
                               value="{{ $item->quantity }}" 
                               min="1" 
                               max="99"
                               onchange="updateQuantity({{ $item->id }}, this.value)"
                               class="form-control text-center mx-2"
                               style="width: 60px;">
                        
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm"
                                onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                {{ $item->quantity >= 99 ? 'disabled' : '' }}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subtotal -->
            <div class="col-8 col-md-1">
                <div class="text-center">
                    <small class="text-muted d-block">Tổng tiền</small>
                    @if($item->productVariant)
                        <div class="price-text fw-bold text-success">
                            {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}₫
                        </div>
                    @else
                        <div class="text-muted">N/A</div>
                    @endif
                </div>
            </div>

            <!-- Remove Button -->
            <div class="col-4 col-md-1">
                <div class="text-center">
                    <button class="btn btn-outline-danger btn-sm" 
                            onclick="removeItem({{ $item->id }})"
                            title="Xóa sản phẩm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile layout summary -->
        <div class="d-md-none mt-3 pt-3 border-top">
            <div class="row">
                <div class="col-6">
                    <small class="text-muted">Số lượng: {{ $item->quantity }}</small>
                </div>
                <div class="col-6 text-end">
                    @if($item->productVariant)
                        <strong class="text-success">
                            {{ number_format($item->productVariant->price * $item->quantity, 0, ',', '.') }}₫
                        </strong>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="total-section">
                        <h5 class="mb-4">
                            <i class="fas fa-calculator me-2"></i>
                            Tổng đơn hàng
                        </h5>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Tạm tính:</span>
                            <span id="subtotal">
                                {{ number_format($cart->sum(function($item) { 
                                    return $item->productVariant ? $item->productVariant->price * $item->quantity : 0; 
                                }), 0, ',', '.') }}₫
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success">Miễn phí</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <h6>Tổng cộng:</h6>
                            <h5 class="price-text" id="total">
                                {{ number_format($cart->sum(function($item) { 
                                    return $item->productVariant ? $item->productVariant->price * $item->quantity : 0; 
                                }), 0, ',', '.') }}₫
                            </h5>
                        </div>

                        <button class="checkout-btn" onclick="checkout()">
                            <i class="fas fa-credit-card me-2"></i>
                            Tiến hành đặt hàng
                        </button>

                        <div class="mt-3 text-center">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Mua hàng an toàn và bảo mật
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p class="mb-4">Hãy thêm một số sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <a href="{{ route('products.showAll') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Khám phá sản phẩm
                </a>
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none" style="background: rgba(0,0,0,0.3); z-index: 9999;">
        <div class="d-flex justify-content-center align-items-center h-100">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CSRF Token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('d-none');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('d-none');
        }

        function showAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }

        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) {
                if (confirm('Bạn có muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    removeItem(itemId);
                }
                return;
            }

            showLoading();

            fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    // Reload page to update totals
                    location.reload();
                } else {
                    showAlert(data.message || 'Có lỗi xảy ra', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi cập nhật giỏ hàng', 'danger');
            });
        }

        function removeItem(itemId) {
            if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
                return;
            }

            showLoading();

            fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    // Remove item from DOM
                    document.querySelector(`[data-item-id="${itemId}"]`).remove();
                    
                    // Check if cart is empty
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    } else {
                        // Update totals
                        updateCartTotals();
                    }
                    
                    showAlert('Đã xóa sản phẩm khỏi giỏ hàng');
                } else {
                    showAlert(data.message || 'Có lỗi xảy ra', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi xóa sản phẩm', 'danger');
            });
        }

        function clearCart() {
            if (!confirm('Bạn có chắc muốn xóa tất cả sản phẩm trong giỏ hàng?')) {
                return;
            }

            showLoading();

            fetch('/cart/clear', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    location.reload();
                } else {
                    showAlert(data.message || 'Có lỗi xảy ra', 'danger');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi xóa giỏ hàng', 'danger');
            });
        }

        function checkout() {
            window.location.href = '/checkout';
        }

        function updateCartTotals() {
            // This function would recalculate totals without page reload
            // For simplicity, we're using location.reload() in the functions above
        }
    </script>
</body>
</html>
