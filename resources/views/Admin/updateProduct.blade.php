<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật sản phẩm - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .variant-group-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s ease;
        }
        .variant-group-card.editing-mode {
            border-left-color: #28a745;
            background-color: #f8fff9;
        }
        .option-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 4px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .option-item input {
            border: none;
            background: transparent;
            outline: none;
            min-width: 80px;
        }
        .option-item input[readonly] {
            background: transparent;
        }
        .option-item:not(.editing-mode) input {
            pointer-events: none;
        }
        .remove-btn {
            color: #dc3545;
            cursor: pointer;
        }
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .locked-group {
            opacity: 0.6;
            background-color: #f8f9fa;
        }
        .locked-group .card-body {
            background-color: #f8f9fa;
        }
        .locked-group input:disabled,
        .locked-group button:disabled {
            cursor: not-allowed;
        }
        .readonly-input {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }
        .edit-group-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .current-image img {
            max-width: 200px;
            height: auto;
            border-radius: 8px;
        }
        .variant-image-preview {
            max-width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        .btn-group-vertical .btn {
            margin-bottom: 5px;
        }
        .alert-info {
            background-color: #e7f3ff;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-edit me-2 text-primary"></i>Cập nhật sản phẩm</h1>
            <a href="{{ route('Admin.products.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('Admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="updateProductForm">
            @csrf
            @method('PUT')

            <!-- Thông tin cơ bản sản phẩm -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="product_name" 
                                       value="{{ old('product_name', $product->product_name) }}" required>
                                @error('product_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Danh mục</label>
                                    <select class="form-select" name="category_id">
                                        <option value="">Chọn danh mục</option>
                                        @if(isset($categories) && $categories->count() > 0)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('category_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select class="form-select" name="is_active">
                                        <option value="true" {{ old('is_active', $product->is_active) === 'true' ? 'selected' : '' }}>
                                            Hiển thị
                                        </option>
                                        <option value="false" {{ old('is_active', $product->is_active) === 'false' ? 'selected' : '' }}>
                                            Ẩn
                                        </option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mô tả sản phẩm</label>
                                <textarea class="form-control" name="description" rows="4" 
                                          placeholder="Nhập mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Ảnh đại diện sản phẩm</label>
                                <div class="current-image mb-2 text-center">
                                    @if($product->image && $product->image !== 'default-product.jpg')
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Ảnh hiện tại" 
                                             class="img-thumbnail">
                                        <div class="small text-muted mt-1">Ảnh hiện tại</div>
                                    @else
                                        <div class="bg-light p-4 border rounded">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                            <p class="mb-0 text-muted mt-2">Chưa có ảnh</p>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <div class="form-text">Chọn ảnh mới để thay đổi (JPG, PNG, GIF - tối đa 2MB)</div>
                                @error('image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quản lý Variant Groups -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Nhóm phân loại sản phẩm</h5>
                    <button type="button" class="btn btn-success btn-sm" onclick="addVariantGroup()">
                        <i class="fas fa-plus me-1"></i>Thêm nhóm
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Sau khi chỉnh sửa nhóm phân loại, hệ thống sẽ tự động tạo lại các biến thể sản phẩm. 
                        Dữ liệu của các biến thể cũ có thể bị mất.
                    </div>
                    
                    <div id="variantGroupsContainer">
                        @forelse($product->variantGroups as $group)
                            <div class="variant-group-card mb-3 border rounded p-3" data-group-id="{{ $group->id }}">
                                <input type="hidden" name="variant_groups[{{ $loop->index }}][id]" value="{{ $group->id }}">
                                
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tên nhóm phân loại</label>
                                            <input type="text" class="form-control group-name-input" 
                                                   name="variant_groups[{{ $loop->index }}][name]" 
                                                   value="{{ $group->name }}" readonly>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Các lựa chọn</label>
                                            <div class="options-container mb-2" data-group-index="{{ $loop->index }}">
                                                @forelse($group->options as $option)
                                                    <div class="option-item" data-option-id="{{ $option->id }}">
                                                        <input type="hidden" name="variant_groups[{{ $loop->parent->index }}][options][{{ $loop->index }}][id]" value="{{ $option->id }}">
                                                        <input type="text" class="option-value-input" 
                                                               name="variant_groups[{{ $loop->parent->index }}][options][{{ $loop->index }}][value]" 
                                                               value="{{ $option->value }}" readonly>
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-option-btn" 
                                                                onclick="removeOption(this)" style="display: none;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @empty
                                                    <div class="text-muted">Chưa có lựa chọn nào</div>
                                                @endforelse
                                            </div>
                                            
                                            <div class="add-option-section mt-2" style="display: none;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control new-option-input" 
                                                           placeholder="Nhập lựa chọn mới...">
                                                    <button type="button" class="btn btn-outline-primary" onclick="addOption(this)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="btn-group-vertical w-100">
                                            <button type="button" class="btn btn-sm btn-outline-primary edit-group-btn" 
                                                    onclick="enableEditGroup(this)">
                                                <i class="fas fa-edit me-1"></i>Sửa
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success save-group-btn" 
                                                    onclick="saveGroup(this)" style="display: none;">
                                                <i class="fas fa-check me-1"></i>Lưu
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="removeVariantGroup(this)">
                                                <i class="fas fa-trash me-1"></i>Xóa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Chưa có nhóm phân loại nào. Nhấn "Thêm nhóm" để tạo mới.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Preview Variants -->
            <div class="card mb-4" id="variantPreviewSection">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Các biến thể sản phẩm</h5>
                    <button type="button" class="btn btn-sm btn-info" onclick="previewVariants()">
                        <i class="fas fa-sync-alt me-1"></i>Xem trước các biến thể mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Khi bạn thay đổi nhóm phân loại hoặc thêm lựa chọn mới, 
                        hệ thống sẽ tự động tạo thêm các biến thể mới khi lưu sản phẩm.
                    </div>
                    
                    <!-- Existing Variants -->
                    @if($product->variants && $product->variants->count() > 0)
                        <h6><i class="fas fa-box me-2"></i>Biến thể hiện có ({{ $product->variants->count() }})</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th style="width: 120px;">SKU</th>
                                        <th>Tổ hợp</th>
                                        <th style="width: 100px;">Giá (₫)</th>
                                        <th style="width: 100px;">Giá so sánh (₫)</th>
                                        <th style="width: 80px;">Số lượng</th>
                                        <th style="width: 100px;">Trạng thái</th>
                                        <th style="width: 120px;">Hình ảnh</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                        <tr data-variant-id="{{ $variant->id }}">
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <code>{{ $variant->sku }}</code>
                                            </td>
                                            <td>
                                                @foreach($variant->values as $value)
                                                    <span class="badge bg-secondary me-1">
                                                        {{ $value->option->group->name }}: {{ $value->option->value }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <input type="hidden" name="variants[{{ $loop->index }}][id]" value="{{ $variant->id }}">
                                                <input type="number" name="variants[{{ $loop->index }}][price]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $variant->price }}" min="0" step="1000">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $loop->index }}][compare_at_price]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $variant->compare_at_price }}" min="0" step="1000">
                                            </td>
                                            <td>
                                                <input type="number" name="variants[{{ $loop->index }}][quantity]" 
                                                       class="form-control form-control-sm" 
                                                       value="{{ $variant->quantity }}" min="0">
                                            </td>
                                            <td>
                                                <select name="variants[{{ $loop->index }}][is_active]" class="form-select form-select-sm">
                                                    <option value="true" {{ $variant->is_active === 'true' ? 'selected' : '' }}>
                                                        Hoạt động
                                                    </option>
                                                    <option value="false" {{ $variant->is_active === 'false' ? 'selected' : '' }}>
                                                        Tạm ngưng
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="current-variant-image mb-1">
                                                    @if($variant->image && $variant->image !== 'default-variant.jpg')
                                                        <img src="{{ asset('storage/' . $variant->image) }}" alt="Variant" 
                                                             class="variant-image-preview">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 60px; height: 60px; border-radius: 4px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="file" name="variants[{{ $loop->index }}][image]" 
                                                       class="form-control form-control-sm" accept="image/*">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    
                    <!-- Preview New Variants -->
                    <div id="newVariantsPreview" style="display: none;">
                        <h6 class="mt-4"><i class="fas fa-plus-circle me-2 text-success"></i>Biến thể mới sẽ được tạo</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-success">
                                    <tr>
                                        <th>STT</th>
                                        <th>SKU dự kiến</th>
                                        <th>Tổ hợp</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody id="newVariantsBody">
                                    <!-- Dynamic content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="card">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg me-3" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Cập nhật sản phẩm
                    </button>
                    <a href="{{ route('Admin.products.list') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Hủy bỏ
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let groupIndex = {{ $product->variantGroups->count() }};
        let optionIndex = {};

        // Initialize option indexes
        @foreach($product->variantGroups as $group)
            optionIndex[{{ $loop->index }}] = {{ $group->options->count() }};
        @endforeach

        function enableEditGroup(button) {
            const card = button.closest('.variant-group-card');
            
            // Enable inputs
            card.querySelector('.group-name-input').removeAttribute('readonly');
            card.querySelectorAll('.option-value-input').forEach(input => {
                input.removeAttribute('readonly');
            });
            
            // Show/hide buttons
            card.querySelectorAll('.remove-option-btn').forEach(btn => {
                btn.style.display = 'inline-block';
            });
            card.querySelector('.add-option-section').style.display = 'block';
            card.querySelector('.edit-group-btn').style.display = 'none';
            card.querySelector('.save-group-btn').style.display = 'inline-block';
            
            card.classList.add('editing-mode');
            card.querySelectorAll('.option-item').forEach(item => {
                item.classList.add('editing-mode');
            });
        }

        function saveGroup(button) {
            const card = button.closest('.variant-group-card');
            const groupNameInput = card.querySelector('.group-name-input');
            
            if (!groupNameInput.value.trim()) {
                alert('Vui lòng nhập tên nhóm phân loại');
                groupNameInput.focus();
                return;
            }
            
            // Check if has at least one option
            const options = card.querySelectorAll('.option-value-input');
            let hasValidOption = false;
            options.forEach(input => {
                if (input.value.trim()) {
                    hasValidOption = true;
                }
            });
            
            if (!hasValidOption) {
                alert('Vui lòng thêm ít nhất một lựa chọn cho nhóm này');
                return;
            }
            
            // Set readonly
            card.querySelector('.group-name-input').setAttribute('readonly', 'readonly');
            card.querySelectorAll('.option-value-input').forEach(input => {
                input.setAttribute('readonly', 'readonly');
            });
            
            // Show/hide buttons
            card.querySelectorAll('.remove-option-btn').forEach(btn => {
                btn.style.display = 'none';
            });
            card.querySelector('.add-option-section').style.display = 'none';
            card.querySelector('.edit-group-btn').style.display = 'inline-block';
            card.querySelector('.save-group-btn').style.display = 'none';
            
            card.classList.remove('editing-mode');
            card.querySelectorAll('.option-item').forEach(item => {
                item.classList.remove('editing-mode');
            });
        }

        function addVariantGroup() {
            const container = document.getElementById('variantGroupsContainer');
            const groupHtml = `
                <div class="variant-group-card mb-3 border rounded p-3 editing-mode">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tên nhóm phân loại</label>
                                <input type="text" class="form-control group-name-input" 
                                       name="variant_groups[${groupIndex}][name]" 
                                       placeholder="Ví dụ: Màu sắc, Kích thước...">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Các lựa chọn</label>
                                <div class="options-container mb-2" data-group-index="${groupIndex}">
                                </div>
                                
                                <div class="add-option-section mt-2">
                                    <div class="input-group">
                                        <input type="text" class="form-control new-option-input" 
                                               placeholder="Nhập lựa chọn mới...">
                                        <button type="button" class="btn btn-outline-primary" onclick="addOption(this)">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="btn-group-vertical w-100">
                                <button type="button" class="btn btn-sm btn-outline-primary edit-group-btn" 
                                        onclick="enableEditGroup(this)" style="display: none;">
                                    <i class="fas fa-edit me-1"></i>Sửa
                                </button>
                                <button type="button" class="btn btn-sm btn-success save-group-btn" 
                                        onclick="saveGroup(this)">
                                    <i class="fas fa-check me-1"></i>Lưu
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="removeVariantGroup(this)">
                                    <i class="fas fa-trash me-1"></i>Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', groupHtml);
            optionIndex[groupIndex] = 0;
            groupIndex++;
        }

        function addOption(button) {
            const input = button.previousElementSibling;
            const value = input.value.trim();
            
            if (!value) {
                alert('Vui lòng nhập giá trị lựa chọn');
                input.focus();
                return;
            }
            
            const container = button.closest('.variant-group-card').querySelector('.options-container');
            const groupIndexAttr = container.getAttribute('data-group-index');
            const currentOptionIndex = optionIndex[groupIndexAttr] || 0;
            
            const optionHtml = `
                <div class="option-item editing-mode" data-option-id="">
                    <input type="text" class="option-value-input" 
                           name="variant_groups[${groupIndexAttr}][options][${currentOptionIndex}][value]" 
                           value="${value}">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-option-btn" 
                            onclick="removeOption(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', optionHtml);
            input.value = '';
            optionIndex[groupIndexAttr] = currentOptionIndex + 1;
        }

        function removeOption(button) {
            if (confirm('Bạn có chắc muốn xóa lựa chọn này?')) {
                button.closest('.option-item').remove();
            }
        }

        function removeVariantGroup(button) {
            if (confirm('Bạn có chắc muốn xóa nhóm phân loại này? Tất cả các biến thể liên quan sẽ bị xóa.')) {
                button.closest('.variant-group-card').remove();
                previewVariants(); // Update preview after removing group
            }
        }

        function previewVariants() {
            const groups = collectVariantGroupsData();
            
            if (groups.length === 0) {
                document.getElementById('newVariantsPreview').style.display = 'none';
                return;
            }
            
            // Generate all combinations
            const combinations = generateCombinations(groups);
            
            if (combinations.length === 0) {
                document.getElementById('newVariantsPreview').style.display = 'none';
                return;
            }
            
            // Filter only new combinations (not existing)
            const newCombinations = filterNewCombinations(combinations);
            
            if (newCombinations.length === 0) {
                document.getElementById('newVariantsPreview').style.display = 'none';
                return;
            }
            
            // Display preview
            displayNewVariantsPreview(newCombinations);
        }

        function collectVariantGroupsData() {
            const groups = [];
            document.querySelectorAll('.variant-group-card').forEach((card, groupIndex) => {
                const groupName = card.querySelector('.group-name-input').value.trim();
                if (!groupName) return;
                
                const options = [];
                card.querySelectorAll('.option-value-input').forEach(input => {
                    const value = input.value.trim();
                    if (value) {
                        options.push({
                            value: value,
                            id: `new_${groupIndex}_${options.length}` // Temporary ID for new options
                        });
                    }
                });
                
                if (options.length > 0) {
                    groups.push({
                        name: groupName,
                        options: options
                    });
                }
            });
            return groups;
        }

        function generateCombinations(groups) {
            if (groups.length === 0) return [];
            
            let combinations = [[]];
            
            groups.forEach(group => {
                const newCombinations = [];
                combinations.forEach(combination => {
                    group.options.forEach(option => {
                        newCombinations.push([...combination, {
                            groupName: group.name,
                            optionValue: option.value,
                            optionId: option.id
                        }]);
                    });
                });
                combinations = newCombinations;
            });
            
            return combinations;
        }

        function filterNewCombinations(combinations) {
            // Get existing variants combinations
            const existingCombinations = [];
            document.querySelectorAll('[data-variant-id]').forEach(row => {
                const badges = row.querySelectorAll('.badge');
                const combination = [];
                badges.forEach(badge => {
                    const text = badge.textContent.trim();
                    if (text.includes(': ')) {
                        const [group, option] = text.split(': ');
                        combination.push({
                            groupName: group,
                            optionValue: option
                        });
                    }
                });
                if (combination.length > 0) {
                    existingCombinations.push(combination);
                }
            });
            
            // Filter new combinations
            return combinations.filter(newCombo => {
                return !existingCombinations.some(existingCombo => {
                    if (existingCombo.length !== newCombo.length) return false;
                    
                    return existingCombo.every(existingItem => {
                        return newCombo.some(newItem => 
                            newItem.groupName === existingItem.groupName && 
                            newItem.optionValue === existingItem.optionValue
                        );
                    });
                });
            });
        }

        function displayNewVariantsPreview(combinations) {
            const tbody = document.getElementById('newVariantsBody');
            const productName = document.querySelector('input[name="product_name"]').value || 'Product';
            
            tbody.innerHTML = '';
            
            combinations.forEach((combination, index) => {
                // Generate SKU preview
                const productId = {{ $product->id }};
                const optionValues = combination.map(item => item.optionValue).join('-');
                const sku = `${productId}-${optionValues}`;
                
                // Generate combination display
                const combinationHtml = combination.map(item => 
                    `<span class="badge bg-success me-1">${item.groupName}: ${item.optionValue}</span>`
                ).join('');
                
                const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td><code class="text-success">${sku}</code></td>
                        <td>${combinationHtml}</td>
                        <td><span class="badge bg-info">Sẽ tạo mới</span></td>
                    </tr>
                `;
                
                tbody.insertAdjacentHTML('beforeend', row);
            });
            
            document.getElementById('newVariantsPreview').style.display = 'block';
        }

        // Auto preview when saving group
        const originalSaveGroup = saveGroup;
        saveGroup = function(button) {
            originalSaveGroup(button);
            setTimeout(previewVariants, 100); // Small delay to ensure DOM is updated
        };

        // Auto preview when adding option
        const originalAddOption = addOption;
        addOption = function(button) {
            originalAddOption(button);
            setTimeout(previewVariants, 100);
        };

        // Form validation
        document.getElementById('updateProductForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang cập nhật...';
        });
    </script>
</body>
</html>
