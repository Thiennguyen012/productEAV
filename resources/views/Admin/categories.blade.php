<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quản lý danh mục - Admin</title>
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
                        <i class="fas fa-tags me-3"></i>
                        Quản lý danh mục
                    </h1>
                    <p class="mb-0 opacity-75">Quản lý các danh mục sản phẩm</p>
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
            <div class="col-md-12">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $categories ? $categories->count() : 0 }}</h4>
                            <small class="text-muted">Tổng danh mục</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-box">
            <form method="GET" action="{{ request()->url() }}" id="filterForm">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="category_name" 
                                   value="{{ request('category_name') }}"
                                   placeholder="Tìm theo tên danh mục...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="sort">
                            <option value="">Sắp xếp theo</option>
                            <option value="id" {{ request('sort') == 'id' ? 'selected' : '' }}>ID</option>
                            <option value="category_name" {{ request('sort') == 'category_name' ? 'selected' : '' }}>Tên danh mục</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>
                            Lọc
                        </button>
                        <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-times"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Summary -->
        @if(request()->hasAny(['category_name', 'sort']))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <div class="flex-grow-1">
                        <strong>Bộ lọc đang áp dụng:</strong>
                        @if(request('category_name'))
                            <span class="badge bg-primary ms-2">Tên: {{ request('category_name') }}</span>
                        @endif
                        @if(request('sort'))
                            <span class="badge bg-info ms-2">Sắp xếp: 
                                {{ 
                                    match(request('sort')) {
                                        'id' => 'ID',
                                        'category_name' => 'Tên danh mục',
                                        'created_at' => 'Ngày tạo',
                                        default => request('sort')
                                    }
                                }}
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

        <!-- Categories Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th style="width: 150px;">Ngày tạo</th>
                            <th style="width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody">
                        @if($categories && $categories->count() > 0)
                            @foreach($categories as $category)
                            <tr data-category-id="{{ $category->id }}">
                                <td>
                                    <strong class="text-primary">#{{ $category->id }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $category->category_name }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-muted">{{ $category->slug }}</code>
                                </td>
                                <td>
                                    <div class="text-muted">
                                        {{ $category->description ? Str::limit($category->description, 80) : '-' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <strong>{{ $category->created_at ? $category->created_at->format('d/m/Y') : '-' }}</strong>
                                        @if($category->created_at)
                                            <br>
                                            <small class="text-muted">{{ $category->created_at->format('H:i') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-info btn-action" 
                                                onclick="editCategory({{ $category->id }})"
                                                title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" 
                                                onclick="deleteCategory({{ $category->id }})"
                                                title="Xóa danh mục">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-tags fa-3x mb-3 opacity-25"></i>
                                        <h5>Chưa có danh mục nào</h5>
                                        <p>Danh sách danh mục trống hoặc không khớp với bộ lọc.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
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

            // Form submit with loading state
            $('#filterForm').on('submit', function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang lọc...');
            });

            // Allow Enter key to submit form in search input
            $('input[name="category_name"]').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    $('#filterForm').submit();
                }
            });
        });

        // Edit category (redirect to edit page)
        function editCategory(categoryId) {
            window.location.href = `/admin/categories/${categoryId}`;
        }

        // Delete category
        function deleteCategory(categoryId) {
            if (!confirm('Bạn có chắc chắn muốn xóa danh mục này? Hành động này không thể hoàn tác.')) {
                return;
            }

            $.ajax({
                url: `/admin/categories/${categoryId}`,
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('danger', response.message);
                    }
                },
                error: function() {
                    showAlert('danger', 'Có lỗi xảy ra khi xóa danh mục');
                }
            });
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
