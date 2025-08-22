<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thêm danh mục mới - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1);
            transform: translateY(-1px);
        }

        .btn-secondary {
            padding: 0.75rem 2rem;
            font-weight: 600;
        }

        .required {
            color: #dc3545;
        }

        .slug-preview {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-family: 'Courier New', monospace;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .alert-fixed {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        .preview-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-plus-circle me-3"></i>
                        Thêm danh mục mới
                    </h1>
                    <p class="mb-0 opacity-75">Tạo danh mục sản phẩm mới cho hệ thống</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('Admin.categories.list') }}" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-4">
        <!-- New Category Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <div class="bg-success rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="mb-0">Thông tin danh mục mới</h4>
                            <p class="text-muted mb-0">Nhập thông tin cho danh mục sản phẩm mới</p>
                        </div>
                    </div>

                    <form id="newCategoryForm">
                        @csrf

                        <!-- Category Name -->
                        <div class="mb-4">
                            <label for="category_name" class="form-label">
                                Tên danh mục <span class="required">*</span>
                            </label>
                            <input type="text"
                                class="form-control"
                                id="category_name"
                                name="category_name"
                                placeholder="Nhập tên danh mục"
                                required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Tên danh mục sẽ được hiển thị cho khách hàng
                            </div>
                        </div>

                        <!-- Slug -->
                        <div class="mb-4">
                            <label for="slug" class="form-label">
                                Slug (URL thân thiện) <span class="required">*</span>
                            </label>
                            <input type="text"
                                class="form-control"
                                id="slug"
                                name="slug"
                                placeholder="vd: thoi-trang-nam"
                                required>
                            <div class="form-text">
                                <i class="fas fa-link me-1"></i>
                                Slug sẽ được tự động tạo từ tên danh mục
                            </div>

                            <!-- Slug Preview -->
                            <div class="mt-2">
                                <small class="text-muted">URL xem trước:</small>
                                <div class="slug-preview" id="slugPreview">
                                    {{ url('/categories/') }}/slug-se-hien-thi-o-day
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label">
                                Mô tả danh mục
                            </label>
                            <textarea class="form-control"
                                id="description"
                                name="description"
                                rows="4"
                                placeholder="Nhập mô tả chi tiết về danh mục này..."></textarea>
                            <div class="form-text">
                                <i class="fas fa-align-left me-1"></i>
                                Mô tả sẽ giúp khách hàng hiểu rõ hơn về danh mục này
                            </div>
                        </div>

                        <!-- Preview Card -->
                        <div class="preview-card" id="previewCard" style="display: none;">
                            <h6 class="mb-3">
                                <i class="fas fa-eye me-2"></i>
                                Xem trước danh mục
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Tên:</strong> 
                                        <span id="previewName">-</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Slug:</strong> 
                                        <code id="previewSlug">-</code>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <p class="mb-0">
                                        <strong>Mô tả:</strong> 
                                        <span id="previewDescription">-</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('Admin.categories.list') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Hủy bỏ
                            </a>

                            <div>
                                <button type="button" class="btn btn-outline-primary me-2" onclick="resetForm()">
                                    <i class="fas fa-eraser me-2"></i>
                                    Xóa form
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>
                                    Tạo danh mục
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Help Card -->
                <div class="form-card">
                    <h5 class="mb-3">
                        <i class="fas fa-lightbulb me-2"></i>
                        Hướng dẫn
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Tên danh mục:</h6>
                            <ul class="small text-muted">
                                <li>Nên ngắn gọn, dễ hiểu</li>
                                <li>Không quá 50 ký tự</li>
                                <li>VD: "Thời trang Nam", "Điện tử"</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Slug URL:</h6>
                            <ul class="small text-muted">
                                <li>Tự động tạo từ tên danh mục</li>
                                <li>Chỉ chứa chữ thường, số và dấu gạch ngang</li>
                                <li>VD: "thoi-trang-nam", "dien-tu"</li>
                            </ul>
                        </div>
                    </div>
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

            // Auto-generate slug from category name
            $('#category_name').on('input', function() {
                const categoryName = $(this).val();
                const slug = generateSlug(categoryName);
                $('#slug').val(slug);
                updateSlugPreview(slug);
                updatePreview();
            });

            // Update slug preview when slug field changes
            $('#slug').on('input', function() {
                const slug = $(this).val();
                updateSlugPreview(slug);
                updatePreview();
            });

            // Update preview when description changes
            $('#description').on('input', function() {
                updatePreview();
            });

            // Form submission with validation
            $('#newCategoryForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = $('#submitBtn');
                const originalText = submitBtn.html();

                // Validate required fields
                const categoryName = $('#category_name').val().trim();
                const slug = $('#slug').val().trim();

                if (!categoryName) {
                    showAlert('danger', 'Vui lòng nhập tên danh mục');
                    $('#category_name').focus();
                    return;
                }

                if (!slug) {
                    showAlert('danger', 'Vui lòng nhập slug');
                    $('#slug').focus();
                    return;
                }

                // Show loading state
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Đang tạo...');

                // Submit form data
                const formData = {
                    category_name: categoryName,
                    slug: slug,
                    description: $('#description').val()
                };

                $.ajax({
                    url: '/admin/categories/new',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message || 'Tạo danh mục thành công!');
                            setTimeout(() => {
                                window.location.href = '{{ route("Admin.categories.list") }}';
                            }, 1500);
                        } else {
                            showAlert('danger', response.message || 'Có lỗi xảy ra');
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra khi tạo danh mục';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                        }

                        showAlert('danger', message);
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Initialize
            updateSlugPreview('');
        });

        // Generate slug from string
        function generateSlug(str) {
            return str
                .toLowerCase()
                .trim()
                .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
                .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
                .replace(/[ìíịỉĩ]/g, 'i')
                .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
                .replace(/[ùúụủũưừứựửữ]/g, 'u')
                .replace(/[ỳýỵỷỹ]/g, 'y')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '');
        }

        // Update slug preview
        function updateSlugPreview(slug) {
            const baseUrl = '{{ url("/categories/") }}';
            const preview = slug || 'slug-se-hien-thi-o-day';
            $('#slugPreview').text(baseUrl + '/' + preview);
        }

        // Update preview card
        function updatePreview() {
            const categoryName = $('#category_name').val().trim();
            const slug = $('#slug').val().trim();
            const description = $('#description').val().trim();

            if (categoryName || slug || description) {
                $('#previewCard').show();
                $('#previewName').text(categoryName || '-');
                $('#previewSlug').text(slug || '-');
                $('#previewDescription').text(description || '-');
            } else {
                $('#previewCard').hide();
            }
        }

        // Reset form
        function resetForm() {
            if (confirm('Bạn có chắc muốn xóa toàn bộ form?')) {
                $('#newCategoryForm')[0].reset();
                updateSlugPreview('');
                $('#previewCard').hide();
            }
        }

        // Show alert function
        function showAlert(type, message) {
            // Remove existing alerts
            $('.alert-fixed').remove();

            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-fixed" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);

            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.alert-fixed').alert('close');
            }, 5000);
        }
    </script>
</body>

</html>
