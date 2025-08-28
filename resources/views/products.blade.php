<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Compact pagination styling */
        .pagination {
            --bs-pagination-padding-x: 0.5rem;
            --bs-pagination-padding-y: 0.25rem;
            --bs-pagination-font-size: 0.875rem;
            --bs-pagination-border-radius: 0.25rem;
        }
        
        .pagination .page-link {
            min-width: 2rem;
            text-align: center;
        }

        /* Sidebar styling */
        .sidebar {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .category-item {
            display: block;
            padding: 8px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        
        .category-item:hover {
            background-color: #e9ecef;
            color: #495057;
            text-decoration: none;
        }
        
        .category-item.active {
            background-color: #007bff;
            color: white;
        }
        
        .filter-header {
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Danh sách sản phẩm</h1>
        
        <!-- Simple Search Bar -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-8">
                <form action="{{ route('products.showAll') }}" method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control me-2" 
                           placeholder="Tìm kiếm sản phẩm theo tên..." 
                           value="{{ request('search') }}"
                           style="border-radius: 25px; padding: 10px 20px;">
                    
                    <!-- Preserve category filter -->
                    @if(request('category_id'))
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    @endif
                    
                    <button type="submit" class="btn btn-primary" style="border-radius: 25px; padding: 10px 20px;">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    @if(request('search') || request('category_id'))
                        <a href="{{ route('products.showAll') }}" class="btn btn-outline-secondary ms-2" style="border-radius: 25px; padding: 10px 20px;">
                            <i class="fas fa-times"></i> Xóa
                        </a>
                    @endif
                </form>
                
                @if(request('search'))
                    <div class="mt-2 text-muted text-center">
                        Kết quả tìm kiếm cho: <strong>"{{ request('search') }}"</strong>
                        @if(method_exists($products, 'total') && $products->total() > 0)
                            <span class="badge bg-primary ms-2">{{ $products->total() }} sản phẩm</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 col-md-4">
                <div class="sidebar">
                    <h5 class="filter-header">
                        <i class="fas fa-filter"></i> Lọc sản phẩm
                    </h5>
                    
                    <!-- Category Filter -->
                    <div class="mb-4">
                        <h6 class="mb-3">Danh mục</h6>
                        <a href="{{ route('products.showAll') }}" 
                           class="category-item {{ !request('category_id') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i> Tất cả danh mục
                            <span class="badge bg-secondary float-end">{{ $products->total() ?? $products->count() }}</span>
                        </a>
                        
                        @if(isset($category) && $category->count() > 0)
                            @foreach($category as $cat)
                                @php
                                    $categoryParams = request()->query();
                                    $categoryParams['category_id'] = $cat->id;
                                    $categoryUrl = route('products.showAll', $categoryParams);
                                @endphp
                                <a href="{{ $categoryUrl }}" 
                                   class="category-item {{ request('category_id') == $cat->id ? 'active' : '' }}">
                                    <i class="fas fa-tag"></i> {{ $cat->category_name }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                    
                    <!-- Clear Filters -->
                    @if(request('category_id') || request('search'))
                        <div class="mb-3">
                            <a href="{{ route('products.showAll') }}" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fas fa-times"></i> Xóa tất cả bộ lọc
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Products Content -->
            <div class="col-lg-9 col-md-8">
                <div class="row">
            @forelse($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="{{ asset('images/' . $product->image) }}" 
                             class="card-img-top" 
                             alt="{{ $product->product_name }}"
                             style="height: 200px; object-fit: cover;">
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->product_name }}</h5>
                            <p class="card-text">{{ $product->description }}</p>
                            
                            <!-- Category -->
                            @if($product->category)
                                <p class="text-muted">
                                    <small><strong>Danh mục:</strong> {{ $product->category->category_name }}</small>
                                </p>
                            @endif
                            
                            <!-- Variants -->
                            @if($product->variants && $product->variants->count() > 0)
                                <div class="mb-3">
                                    <h6>Biến thể có sẵn ({{ $product->variants->count() }}):</h6>
                                    @foreach($product->variants as $variant)
                                        <div class="border rounded p-2 mb-2">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>{{ $variant->sku }}</strong>
                                                    @if($variant->options && $variant->options->count() > 0)
                                                        <br>
                                                        @foreach($variant->options as $option)
                                                            <span class="badge bg-secondary">{{ $option->value }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-danger fw-bold">
                                                        {{ number_format($variant->price, 0, ',', '.') }}₫
                                                    </div>
                                                    <small class="text-muted">SL: {{ $variant->quantity }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <!-- Variant Groups -->
                            @if($product->variantGroups && $product->variantGroups->count() > 0)
                                <div class="mb-3">
                                    <h6>Tùy chọn:</h6>
                                    @foreach($product->variantGroups as $group)
                                        <div class="mb-2">
                                            <strong>{{ $group->name }}:</strong>
                                            @if($group->options && $group->options->count() > 0)
                                                @foreach($group->options as $option)
                                                    <span class="badge bg-light text-dark">{{ $option->value }}</span>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-auto">
                                <a href="{{ route('products.detail', $product->slug) }}" class="btn btn-primary w-100">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        @if(request('search'))
                            <h4>Không tìm thấy sản phẩm</h4>
                            <p>Không có sản phẩm nào phù hợp với từ khóa "<strong>{{ request('search') }}</strong>"</p>
                            <a href="{{ route('products.showAll') }}" class="btn btn-primary">
                                Xem tất cả sản phẩm
                            </a>
                        @else
                            <h4>Không có sản phẩm nào</h4>
                            <p>Hiện tại chưa có sản phẩm nào trong hệ thống.</p>
                        @endif
                    </div>
                </div>
            @endforelse
                </div>
                
                <!-- Pagination -->
                @if(method_exists($products, 'links') && $products->hasPages())
                    <div class="d-flex justify-content-center my-4">
                        {{ $products->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                @endif
                
                <!-- Products Info -->
                <div class="mt-4">
                    <div class="alert alert-light">
                        @if(method_exists($products, 'total'))
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Hiển thị:</strong> {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} 
                                    trong tổng số {{ $products->total() }} sản phẩm
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <strong>Trang:</strong> {{ $products->currentPage() }} / {{ $products->lastPage() }}
                                </div>
                            </div>
                        @else
                            <strong>Tổng số sản phẩm:</strong> {{ $products->count() }}
                        @endif
                    </div>
                </div>
            </div> <!-- End col-lg-9 -->
        </div> <!-- End main row -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>