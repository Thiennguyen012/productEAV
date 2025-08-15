<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .badge-status {
            font-size: 0.75rem;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        /* Simple Clean Pagination Styling */
        .pagination {
            margin-bottom: 0;
            gap: 0.25rem;
        }
        
        .pagination .page-item {
            margin: 0 2px;
        }
        
        .pagination .page-link {
            position: relative;
            display: block;
            color: #6c757d;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
            transition: all 0.2s ease-in-out;
        }
        
        .pagination .page-link:hover {
            color: #0056b3;
            background-color: #f8f9fa;
            border-color: #bbb;
            text-decoration: none;
        }
        
        .pagination .page-link:focus {
            color: #0056b3;
            background-color: #f8f9fa;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .pagination .page-item.active .page-link {
            z-index: 3;
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .pagination .page-item.active .page-link:hover {
            color: #fff;
            background-color: #0056b3;
            border-color: #0056b3;
        }
        
        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }
        
        /* Navigation buttons styling */
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding: 0.5rem 1rem;
            font-weight: 600;
        }
        
        /* Pagination Container */
        .pagination-container-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 20px 0;
            background: white;
        }

        .pagination-info-left, .pagination-info-right {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .pagination-nav {
            flex: 1;
            display: flex;
            justify-content: center;
        }

        .custom-pagination {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .custom-pagination .page-link {
            color: #333;
            background-color: white;
            border: 1px solid #ddd;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 35px;
            height: 35px;
        }

        .custom-pagination .page-link:hover {
            color: #007bff;
            background-color: #f8f9fa;
            border-color: #007bff;
        }

        .custom-pagination .page-item.active .page-link {
            color: white;
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
        }

        .custom-pagination .page-item.disabled .page-link {
            color: #999;
            background-color: #f8f9fa;
            border-color: #ddd;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .custom-pagination .page-item.disabled .page-link:hover {
            color: #999;
            background-color: #f8f9fa;
            border-color: #ddd;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .pagination-container-custom {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .pagination-nav {
                order: 2;
            }
            
            .pagination-info-left {
                order: 1;
            }
            
            .pagination-info-right {
                order: 3;
            }
        }        /* Responsive pagination */
        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                min-width: 35px;
                font-size: 0.75rem;
            }
            
            .pagination .page-item:first-child .page-link,
            .pagination .page-item:last-child .page-link {
                padding: 0.375rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="fas fa-box me-2 text-primary"></i>
                Quản lý sản phẩm
            </h1>
            <div>
                <a class="btn btn-success me-2" href="{{ route('Admin.products.showNew') }}">
                    <i class="fas fa-plus me-1"></i>
                    Thêm sản phẩm
                </a>
                <button class="btn btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>
                    Xuất Excel
                </button>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ request()->url() }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="product_name" 
                                       value="{{ request('product_name') }}"
                                       placeholder="Tên sản phẩm, SKU...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id">
                                <option value="">Tất cả danh mục</option>
                                @if(isset($categories) && $categories)
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Trạng thái</label>
                            <select class="form-select" name="status">
                                <option value="">Tất cả</option>
                                <option value="true" {{ request('status') === 'true' ? 'selected' : '' }}>Đang bán</option>
                                <option value="false" {{ request('status') === 'false' ? 'selected' : '' }}>Ngừng bán</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>
                                Lọc
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Summary -->
        @if(request()->hasAny(['product_name', 'category_id', 'status']))
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-filter me-2 text-primary"></i>
                    <strong class="me-2">Bộ lọc đang áp dụng:</strong>
                    @if(request('product_name'))
                        <span class="badge bg-primary me-2">Tìm: {{ request('product_name') }}</span>
                    @endif
                    @if(request('category_id') && isset($categories))
                        @php
                            $selectedCategory = $categories->where('id', request('category_id'))->first();
                        @endphp
                        @if($selectedCategory)
                            <span class="badge bg-success me-2">Danh mục: {{ $selectedCategory->category_name }}</span>
                        @endif
                    @endif
                    @if(request('status'))
                        <span class="badge bg-info me-2">
                            Trạng thái: {{ request('status') === 'true' ? 'Đang bán' : 'Ngừng bán' }}
                        </span>
                    @endif
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Products Table -->
        <div class="card">
            <div class="card-body">
                <!-- Table Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted">
                        Hiển thị {{ $products->count() }} sản phẩm
                    </span>
                    <div>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option value="10">10 / trang</option>
                            <option value="25">25 / trang</option>
                            <option value="50">50 / trang</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" class="form-check-input">
                                </th>
                                <th width="80">Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th width="120">Danh mục</th>
                                <th width="100">Biến thể</th>
                                <th width="120">Giá (Min-Max)</th>
                                <th width="100">Tổng kho</th>
                                <th width="100">Trạng thái</th>
                                <th width="100">Cập nhật</th>
                                <th width="120">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input" value="{{ $product->id }}">
                                    </td>
                                    <td>
                                        <img src="{{ asset('images/' . $product->image) }}" 
                                             class="product-image" 
                                             alt="{{ $product->product_name }}">
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">{{ $product->product_name }}</h6>
                                            <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->category)
                                            <span class="badge bg-info">{{ $product->category->category_name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($product->variants && $product->variants->count() > 0)
                                            <span class="badge bg-primary">{{ $product->variants->count() }}</span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->variants && $product->variants->count() > 0)
                                            @php
                                                $minPrice = $product->variants->min('price');
                                                $maxPrice = $product->variants->max('price');
                                            @endphp
                                            <div class="text-nowrap">
                                                @if($minPrice == $maxPrice)
                                                    <strong class="text-success">{{ number_format($minPrice, 0, ',', '.') }}₫</strong>
                                                @else
                                                    <strong class="text-success">{{ number_format($minPrice, 0, ',', '.') }}₫</strong>
                                                    <br><small class="text-muted">- {{ number_format($maxPrice, 0, ',', '.') }}₫</small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($product->variants && $product->variants->count() > 0)
                                            @php
                                                $totalStock = $product->variants->sum('quantity');
                                            @endphp
                                            <span class="fw-bold {{ $totalStock > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $totalStock }}
                                            </span>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->is_active === 'true')
                                            <span class="badge bg-success badge-status">Đang bán</span>
                                        @else
                                            <span class="badge bg-danger badge-status">Ngừng bán</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $product->updated_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a class="btn btn-outline-info btn-action" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a class="btn btn-outline-warning btn-action" title="Chỉnh sửa" href="{{ route('Admin.products.edit',['id' => $product->id]) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a class="btn btn-outline-danger btn-action" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <h5>Chưa có sản phẩm nào</h5>
                                            <p>Hãy thêm sản phẩm đầu tiên của bạn</p>
                                            <button class="btn btn-primary">
                                                <i class="fas fa-plus me-1"></i>
                                                Thêm sản phẩm
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($products, 'links'))
                    {{ $products->appends(request()->query())->links('pagination::custom') }}
                @endif
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="me-3">Thao tác với các mục đã chọn:</span>
                    <button class="btn btn-outline-success btn-sm me-2">
                        <i class="fas fa-check me-1"></i>
                        Kích hoạt
                    </button>
                    <button class="btn btn-outline-warning btn-sm me-2">
                        <i class="fas fa-pause me-1"></i>
                        Tạm dừng
                    </button>
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash me-1"></i>
                        Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select all checkbox functionality
        document.querySelector('thead input[type="checkbox"]').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Form submit with loading state
        document.getElementById('filterForm').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang lọc...';
        });

        // Allow Enter key to submit form in search input
        document.querySelector('input[name="product_name"]').addEventListener('keypress', function(e) {
            if (e.which === 13) { // Enter key
                document.getElementById('filterForm').submit();
            }
        });

        // Product actions
        function viewProduct(productId) {
            // Add view product functionality
            console.log('View product:', productId);
        }

        function editProduct(productId) {
            window.location.href = `/admin/products/${productId}/edit`;
        }

        function deleteProduct(productId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                // Add delete functionality via AJAX
                console.log('Delete product:', productId);
            }
        }

        // Bulk actions
        function performBulkAction(action) {
            const selectedProducts = Array.from(document.querySelectorAll('tbody input[type="checkbox"]:checked'))
                .map(checkbox => checkbox.value);

            if (selectedProducts.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm');
                return;
            }

            const actionNames = {
                'activate': 'kích hoạt',
                'deactivate': 'tạm dừng',
                'delete': 'xóa'
            };

            if (confirm(`Bạn có chắc chắn muốn ${actionNames[action]} ${selectedProducts.length} sản phẩm đã chọn?`)) {
                // Add bulk action functionality via AJAX
                console.log(`${action} products:`, selectedProducts);
            }
        }

        // Update bulk action buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.btn-outline-success').addEventListener('click', function() {
                performBulkAction('activate');
            });

            document.querySelector('.btn-outline-warning').addEventListener('click', function() {
                performBulkAction('deactivate');
            });

            document.querySelector('.btn-outline-danger').addEventListener('click', function() {
                performBulkAction('delete');
            });
        });
    </script>
</body>
</html>
