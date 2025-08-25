<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý đơn hàng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        .table-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .order-status {
            padding: 0.35rem 0.75rem !important;
            border-radius: 12px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            display: inline-block !important;
            letter-spacing: 0.5px !important;
            border: none !important;
        }
        .status-pending {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e) !important;
            color: #2d3436 !important;
            box-shadow: 0 2px 4px rgba(253, 203, 110, 0.3) !important;
        }
        .status-confirmed {
            background: linear-gradient(135deg, #74b9ff, #0984e3) !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(116, 185, 255, 0.4) !important;
        }
        .status-shipping {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7) !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(162, 155, 254, 0.4) !important;
        }
        .status-delivered {
            background: linear-gradient(135deg, #00b894, #00a085) !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(0, 184, 148, 0.4) !important;
        }
        .status-cancelled {
            background: linear-gradient(135deg, #fd79a8, #e84393) !important;
            color: white !important;
            box-shadow: 0 2px 4px rgba(253, 121, 168, 0.4) !important;
        }
        .payment-cod {
            background: linear-gradient(135deg, #fdcb6e, #e17055) !important;
            color: white !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 12px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            display: inline-block !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(253, 203, 110, 0.3) !important;
        }
        .payment-transfer {
            background: linear-gradient(135deg, #00b894, #00a085) !important;
            color: white !important;
            padding: 0.35rem 0.75rem !important;
            border-radius: 12px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            display: inline-block !important;
            letter-spacing: 0.5px !important;
            box-shadow: 0 2px 4px rgba(0, 184, 148, 0.3) !important;
        }
        .btn-action {
            padding: 0.3rem 0.6rem;
            border-radius: 5px;
            font-size: 0.8rem;
            margin: 0 0.1rem;
        }
        .search-box {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: #495057;
        }
        .table td {
            vertical-align: middle;
            border-color: #eee;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="mb-0">
                        <i class="fas fa-shopping-bag me-3"></i>
                        Quản lý đơn hàng
                    </h1>
                    <p class="mb-0 opacity-75">Theo dõi và quản lý tất cả đơn hàng</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('Admin.products.list') }}" class="btn btn-outline-light me-2">
                        <i class="fas fa-box me-2"></i>
                        Sản phẩm
                    </a>
                    <a href="{{ url('admin') }}" class="btn btn-light">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $orderList ? $orderList->total() : 0 }}</h4>
                            <small class="text-muted">Tổng đơn hàng</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $orderList ? $orderList->where('status', 'pending')->count() : 0 }}</h4>
                            <small class="text-muted">Chờ xử lý</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $orderList ? $orderList->where('status', 'shipping')->count() : 0 }}</h4>
                            <small class="text-muted">Đang giao</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $orderList ? $orderList->where('status', 'delivered')->count() : 0 }}</h4>
                            <small class="text-muted">Hoàn thành</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Summary -->
        @if(request()->hasAny(['customer_name', 'status', 'sort']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <div class="flex-grow-1">
                        <strong>Bộ lọc đang áp dụng:</strong>
                        @if(request('customer_name'))
                            <span class="badge bg-primary ms-2">Tên: {{ request('customer_name') }}</span>
                        @endif
                        @if(request('status'))
                            <span class="badge bg-success ms-2">Trạng thái: 
                                {{ 
                                    match(request('status')) {
                                        'pending' => 'Chờ xử lý',
                                        'confirmed' => 'Đã xác nhận', 
                                        'shipping' => 'Đang giao',
                                        'delivered' => 'Đã giao',
                                        'cancelled' => 'Đã hủy',
                                        default => request('status')
                                    }
                                }}
                            </span>
                        @endif
                        @if(request('sort'))
                            <span class="badge bg-info ms-2">Sắp xếp: 
                                {{ 
                                    match(request('sort')) {
                                        'id' => 'Mã đơn hàng',
                                        'customer_name' => 'Tên khách hàng',
                                        'total' => 'Tổng tiền',
                                        'created_at' => 'Ngày tạo',
                                        default => request('sort')
                                    }
                                }}
                                ({{ request('direction', 'desc') == 'desc' ? 'Giảm dần' : 'Tăng dần' }})
                            </span>
                        @endif
                    </div>
                    <a href="{{ request()->url() }}" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-times me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Search and Filters -->
        <div class="search-box">
            <form method="GET" action="{{ request()->url() }}" id="filterForm">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="customer_name" 
                                   value="{{ request('customer_name') }}"
                                   placeholder="Tìm theo tên khách hàng...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="sort">
                            <option value="">Sắp xếp theo</option>
                            <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>Mã đơn hàng</option>
                            <option value="customer_name" {{ request('sort') == 'customer_name' ? 'selected' : '' }}>Tên khách hàng</option>
                            <option value="total" {{ request('sort') == 'total' ? 'selected' : '' }}>Tổng tiền</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="direction">
                            <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                            <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>
                            Lọc
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-100" title="Xóa bộ lọc">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Mã đơn</th>
                            <th>Khách hàng</th>
                            <th style="width: 130px;">Ngày đặt</th>
                            <th style="width: 120px;">Tổng tiền</th>
                            <th style="width: 120px;">Thanh toán</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        @if($orderList && $orderList->count() > 0)
                            @foreach($orderList as $order)
                            <tr data-order-id="{{ $order->id }}">
                                <td>
                                    <strong class="text-primary">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $order->customer_name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $order->customer_phone }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ $order->created_at->format('d/m/Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($order->total) }}₫</strong>
                                </td>
                                <td>
                                    <span class="badge {{ $order->payment_method == 'offline' ? 'payment-cod' : 'payment-transfer' }}">
                                        {{ $order->payment_method == 'offline' ? 'COD' : 'Chuyển khoản' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge order-status status-{{ $order->status ?? 'pending' }}">
                                        {{ 
                                            match($order->status ?? 'pending') {
                                                'pending' => 'Chờ xử lý',
                                                'confirmed' => 'Đã xác nhận', 
                                                'shipping' => 'Đang giao',
                                                'delivered' => 'Đã giao',
                                                'cancelled' => 'Đã hủy',
                                                default => 'Chờ xử lý'
                                            }
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-action" 
                                                onclick="viewOrderDetails({{ $order->id }})"
                                                title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-success btn-action" 
                                                onclick="updateOrderStatus({{ $order->id }}, 'confirmed')"
                                                title="Xác nhận đơn hàng"
                                                {{ ($order->status ?? 'pending') != 'pending' ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-info btn-action" 
                                                onclick="updateOrderStatus({{ $order->id }}, 'shipping')"
                                                title="Chuyển giao hàng"
                                                {{ !in_array($order->status ?? 'pending', ['pending', 'confirmed']) ? 'disabled' : '' }}>
                                            <i class="fas fa-truck"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" 
                                                onclick="updateOrderStatus({{ $order->id }}, 'cancelled')"
                                                title="Hủy đơn hàng"
                                                {{ in_array($order->status ?? 'pending', ['delivered', 'cancelled']) ? 'disabled' : '' }}>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-shopping-cart fa-3x mb-3 opacity-25"></i>
                                        <h5>Chưa có đơn hàng nào</h5>
                                        <p>Các đơn hàng sẽ xuất hiện ở đây khi khách hàng đặt hàng.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination (if needed) -->
        @if($orderList && method_exists($orderList, 'links'))
            <div class="d-flex justify-content-center mt-4">
                {{ $orderList->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-invoice me-2"></i>
                        Chi tiết đơn hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="printOrder()">
                        <i class="fas fa-print me-2"></i>In đơn hàng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Show loading on form submit only
            $('#filterForm').on('submit', function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang lọc...');
            });

            // Allow Enter key to submit form in search input
            $('input[name="customer_name"]').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $('#filterForm').submit();
                }
            });
        });

        // View order details
        function viewOrderDetails(orderId) {
            $('#orderDetailsModal').modal('show');
            
            // Show loading state
            $('#orderDetailsContent').html(`
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải chi tiết đơn hàng...</p>
                </div>
            `);
            
            // Load order details via AJAX  
            $.get(`/admin/order/${orderId}`)
                .done(function(data) {
                    $('#orderDetailsContent').html(data);
                })
                .fail(function(xhr) {
                    let errorMessage = 'Không thể tải chi tiết đơn hàng';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    $('#orderDetailsContent').html(`
                        <div class="text-center text-danger p-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Lỗi tải dữ liệu</h5>
                            <p>${errorMessage}</p>
                            <button class="btn btn-primary" onclick="viewOrderDetails(${orderId})">
                                <i class="fas fa-redo me-2"></i>Thử lại
                            </button>
                        </div>
                    `);
                });
        }

        // Update order status
        function updateOrderStatus(orderId, newStatus) {
            const statusNames = {
                'confirmed': 'xác nhận',
                'shipping': 'chuyển sang giao hàng',
                'delivered': 'hoàn thành',
                'cancelled': 'hủy'
            };

            if (!confirm(`Bạn có chắc muốn ${statusNames[newStatus]} đơn hàng #${orderId.toString().padStart(6, '0')}?`)) {
                return;
            }

            // Show loading
            const button = $(event.target).closest('button');
            const originalHtml = button.html();
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/admin/order/${orderId}`,
                method: 'PUT',
                data: { 
                    status: newStatus 
                },
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message || 'Cập nhật trạng thái thành công!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', response.message || 'Cập nhật thất bại');
                        button.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr) {
                    let message = 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showAlert('danger', message);
                    button.prop('disabled', false).html(originalHtml);
                }
            });
        }

        // Print order
        function printOrder() {
            const content = $('#orderDetailsContent').html();
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>In đơn hàng</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            @media print {
                                .btn, .modal-footer { display: none !important; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container mt-4">
                            ${content}
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }

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

            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }
    </script>
</body>
</html>
