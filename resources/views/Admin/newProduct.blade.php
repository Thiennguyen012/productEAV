<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .variant-group-card {
            border-left: 4px solid #007bff;
        }
        .option-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 4px;
            display: inline-block;
        }
        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            margin-left: 8px;
        }
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* CSS cho nhóm bị khóa */
        .locked-group {
            opacity: 0.6;
            background-color: #f8f9fa;
            transition: opacity 0.3s ease, background-color 0.3s ease;
        }
        
        .locked-group .card-body {
            background-color: #f8f9fa;
        }
        
        .locked-group input:disabled,
        .locked-group button:disabled {
            cursor: not-allowed;
        }
        
        /* CSS cho readonly input */
        .readonly-input {
            background-color: #e9ecef !important;
            cursor: not-allowed;
        }
        
        /* Hiệu ứng hover cho nút sửa */
        .edit-group-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới</h1>
            <a href="{{ route('Admin.products.list') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <form action="{{ route('Admin.products.new') }}" method="POST" enctype="multipart/form-data" id="productForm">
            @csrf

            <!-- Thông tin cơ bản sản phẩm -->
            <div class="form-section">
                <h4 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin cơ bản</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên sản phẩm *</label>
                        <input type="text" name="product_name" class="form-control" placeholder="Nhập tên sản phẩm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Danh mục *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Chọn danh mục</option>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            @else
                                <option value="" disabled>Không có danh mục nào</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả sản phẩm</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">Chọn ảnh JPG, PNG (tối đa 2MB)</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="is_active" class="form-select">
                            <option value="true">Hiển thị</option>
                            <option value="false">Ẩn</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Nhóm phân loại -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-layer-group me-2"></i>Nhóm phân loại sản phẩm</h4>
                    <button type="button" class="btn btn-success" onclick="addVariantGroup()">
                        <i class="fas fa-plus me-1"></i>Thêm nhóm
                    </button>
                </div>
                
                <div id="variantGroupsContainer">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Thêm các nhóm phân loại như: Màu sắc, Kích thước, Chất liệu... 
                        <br><strong>Lưu ý:</strong> Nhấn nút <strong>"Lưu"</strong> sau khi hoàn thành mỗi nhóm để xem trước các biến thể sẽ được tạo.
                    </div>
                </div>
            </div>

            <!-- Xem trước variants -->
            <div class="form-section" id="previewSection" style="display: none;">
                <h4 class="mb-3"><i class="fas fa-eye me-2"></i>Xem trước biến thể sẽ được tạo</h4>
                <div id="variantPreview"></div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Các biến thể sẽ được tạo với giá và số lượng mặc định = 0. Bạn có thể chỉnh sửa sau khi tạo sản phẩm.
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save me-2"></i>Tạo sản phẩm
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let groupIndex = 0;

        function addVariantGroup() {
            const container = document.getElementById('variantGroupsContainer');
            
            // Khóa tất cả các nhóm hiện có (không làm mất dữ liệu)
            lockAllGroupsSimple();
            
            // Lấy giá trị từ ô input "Tên nhóm phân loại" cuối cùng (nếu có)
            let lastGroupName = '';
            const lastGroupNameInput = container.querySelector('.variant-group-card:last-child .group-name');
            if (lastGroupNameInput) {
                lastGroupName = lastGroupNameInput.value;
            }
            
            const groupHtml = `
                <div class="variant-group-card card mb-3" data-group-index="${groupIndex}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="flex-grow-1 me-3">
                                <label class="form-label">Tên nhóm phân loại</label>
                                <input type="text" name="variant_groups[${groupIndex}][name]" 
                                       class="form-control group-name" 
                                       placeholder="VD: Màu sắc, Kích thước, Chất liệu..." 
                                       value="${lastGroupName}"
                                       required>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success btn-sm edit-group-btn" onclick="enableEditGroup(this)" style="display: none;">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm save-group-btn" onclick="saveGroup(this)">
                                    <i class="fas fa-check"></i> Lưu
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantGroup(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Các lựa chọn</label>
                            <div class="variant-options-container" data-group="${groupIndex}">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control option-input" placeholder="VD: Đỏ, Xanh, Vàng...">
                                    <button type="button" class="btn btn-outline-primary" onclick="addOptionToGroup(${groupIndex})">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="options-list" data-group="${groupIndex}"></div>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', groupHtml);
            groupIndex++;
        }

        function removeVariantGroup(button) {
            if (confirm('Bạn có chắc muốn xóa nhóm này không?')) {
                button.closest('.variant-group-card').remove();
                // Preview sẽ được cập nhật khi nhấn "Lưu" nhóm khác
            }
        }

        function addOptionToGroup(groupIndex) {
            const container = document.querySelector(`[data-group="${groupIndex}"] .option-input`);
            const optionValue = container.value.trim();
            
            if (!optionValue) {
                alert('Vui lòng nhập tên lựa chọn');
                return;
            }
            
            // Kiểm tra trùng lặp
            const existingOptions = document.querySelectorAll(`[data-group="${groupIndex}"] .option-item`);
            for (let option of existingOptions) {
                if (option.textContent.replace('×', '').trim() === optionValue) {
                    alert('Lựa chọn này đã tồn tại');
                    return;
                }
            }
            
            const optionsList = document.querySelector(`.options-list[data-group="${groupIndex}"]`);
            const optionHtml = `
                <span class="option-item">
                    ${optionValue}
                    <span class="remove-btn" onclick="removeOption(this)">×</span>
                    <input type="hidden" name="variant_groups[${groupIndex}][options][]" value="${optionValue}">
                </span>
            `;
            
            optionsList.innerHTML += optionHtml;
            container.value = '';
            // Preview sẽ được cập nhật khi nhấn "Lưu" nhóm
        }

        function removeOption(button) {
            button.closest('.option-item').remove();
            // Preview sẽ được cập nhật khi nhấn "Lưu" nhóm
        }

        function updatePreview() {
            const groups = [];
            
            // Chỉ lấy các nhóm đã được "lưu" (có class locked-group hoặc có nút sửa hiển thị)
            document.querySelectorAll('.variant-group-card').forEach(card => {
                const editBtn = card.querySelector('.edit-group-btn');
                const isLocked = card.classList.contains('locked-group') || 
                               (editBtn && editBtn.style.display !== 'none');
                
                // Chỉ xử lý các nhóm đã được lưu
                if (isLocked) {
                    const groupName = card.querySelector('.group-name').value;
                    const options = [];
                    
                    card.querySelectorAll('.option-item').forEach(item => {
                        const optionText = item.textContent.replace('×', '').trim();
                        if (optionText) options.push(optionText);
                    });
                    
                    if (groupName && options.length > 0) {
                        groups.push({ name: groupName, options: options });
                    }
                }
            });
            
            if (groups.length === 0) {
                document.getElementById('previewSection').style.display = 'none';
                return;
            }
            
            // Tính toán tổ hợp
            let combinations = [[]];
            groups.forEach(group => {
                const temp = [];
                combinations.forEach(combo => {
                    group.options.forEach(option => {
                        temp.push([...combo, { group: group.name, option: option }]);
                    });
                });
                combinations = temp;
            });
            
            // Hiển thị preview
            const previewHtml = `
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="15%">SKU (tự động)</th>
                                <th width="20%">Tổ hợp</th>
                                <th width="12%">Giá</th>
                                <th width="12%">Giá so sánh</th>
                                <th width="10%">Số lượng</th>
                                <th width="10%">Trạng thái</th>
                                <th width="16%">Hình ảnh</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${combinations.map((combo, index) => {
                                const skuPreview = combo.map(c => c.option).join('-');
                                return `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><code class="text-primary">${skuPreview}</code></td>
                                    <td>
                                        ${combo.map(c => `<span class="badge bg-secondary me-1">${c.group}: ${c.option}</span>`).join('')}
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   name="variants[${index}][price]" 
                                                   class="form-control" 
                                                   value="0" 
                                                   min="0"
                                                   placeholder="Giá">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   name="variants[${index}][compare_at_price]" 
                                                   class="form-control" 
                                                   value="0" 
                                                   min="0"
                                                   placeholder="Giá so sánh">
                                            <span class="input-group-text">₫</span>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="variants[${index}][quantity]" 
                                               class="form-control form-control-sm" 
                                               value="0" 
                                               min="0"
                                               placeholder="SL">
                                    </td>
                                    <td>
                                        <select name="variants[${index}][is_active]" class="form-select form-select-sm">
                                            <option value="true" selected>Hoạt động</option>
                                            <option value="false">Tạm ngưng</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="file" 
                                               name="variants[${index}][image]" 
                                               class="form-control form-control-sm" 
                                               accept="image/*"
                                               title="Chọn hình ảnh cho biến thể">
                                        <input type="hidden" 
                                               name="variants[${index}][combination]" 
                                               value="${combo.map(c => c.option).join('-')}">
                                    </td>
                                </tr>
                            `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Sẽ tạo ${combinations.length} biến thể sản phẩm</strong> - Bạn có thể chỉnh sửa thông tin cho từng biến thể ở bảng trên.
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small><strong>Lưu ý:</strong> Giá và số lượng mặc định là 0. Hãy cập nhật thông tin phù hợp trước khi tạo sản phẩm.</small>
                </div>
            `;
            
            document.getElementById('variantPreview').innerHTML = previewHtml;
            document.getElementById('previewSection').style.display = 'block';
        }

        // Hàm khóa đơn giản - chỉ thay đổi giao diện mà không disable input
        function lockAllGroupsSimple() {
            const allGroups = document.querySelectorAll('.variant-group-card');
            allGroups.forEach(group => {
                lockGroupSimple(group);
            });
        }

        // Hàm khóa một nhóm đơn giản
        function lockGroupSimple(groupElement) {
            // Chỉ disable buttons, KHÔNG disable inputs để tránh mất dữ liệu
            const buttons = groupElement.querySelectorAll('button:not(.edit-group-btn):not([onclick*="removeVariantGroup"])');
            
            buttons.forEach(button => {
                button.disabled = true;
            });

            // Thêm class để làm mờ
            groupElement.classList.add('locked-group');
            
            // Thêm lớp readonly cho inputs thay vì disable
            const inputs = groupElement.querySelectorAll('input');
            inputs.forEach(input => {
                input.setAttribute('readonly', 'readonly');
                input.classList.add('readonly-input');
            });
            
            // Ẩn nút Lưu, hiện nút Sửa
            const saveBtn = groupElement.querySelector('.save-group-btn');
            const editBtn = groupElement.querySelector('.edit-group-btn');
            if (saveBtn) saveBtn.style.display = 'none';
            if (editBtn) editBtn.style.display = 'inline-block';
        }

        // Hàm mở khóa một nhóm để chỉnh sửa (đơn giản)
        function enableEditGroup(button) {
            const groupElement = button.closest('.variant-group-card');
            
            // Enable tất cả các button trong nhóm
            const buttons = groupElement.querySelectorAll('button:not(.edit-group-btn)');
            buttons.forEach(button => {
                button.disabled = false;
            });

            // Xóa readonly từ inputs
            const inputs = groupElement.querySelectorAll('input');
            inputs.forEach(input => {
                input.removeAttribute('readonly');
                input.classList.remove('readonly-input');
            });

            // Xóa class làm mờ
            groupElement.classList.remove('locked-group');
            
            // Hiện nút Lưu, ẩn nút Sửa
            const saveBtn = groupElement.querySelector('.save-group-btn');
            const editBtn = groupElement.querySelector('.edit-group-btn');
            if (saveBtn) saveBtn.style.display = 'inline-block';
            if (editBtn) editBtn.style.display = 'none';
        }

        // Hàm lưu nhóm (khóa lại sau khi chỉnh sửa)
        function saveGroup(button) {
            const groupElement = button.closest('.variant-group-card');
            
            // Kiểm tra validation
            const groupNameInput = groupElement.querySelector('.group-name');
            if (!groupNameInput.value.trim()) {
                alert('Vui lòng nhập tên nhóm phân loại');
                groupNameInput.focus();
                return;
            }

            // Khóa nhóm bằng phương pháp đơn giản
            lockGroupSimple(groupElement);
            
            // Cập nhật preview
            updatePreview();
        }

        // Hàm utilities cho variant
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
        }

        // Hàm validation form trước submit
        function validateForm() {
            const productName = document.querySelector('input[name="product_name"]').value.trim();
            if (!productName) {
                alert('Vui lòng nhập tên sản phẩm');
                return false;
            }

            const groups = document.querySelectorAll('.variant-group-card');
            if (groups.length === 0) {
                alert('Vui lòng thêm ít nhất một nhóm phân loại');
                return false;
            }

            // Kiểm tra mỗi group có ít nhất một option
            for (let group of groups) {
                const options = group.querySelectorAll('.option-item');
                if (options.length === 0) {
                    alert('Mỗi nhóm phân loại phải có ít nhất một lựa chọn');
                    return false;
                }
            }

            return true;
        }

        // Xử lý Enter trong option input
        document.addEventListener('keypress', function(e) {
            if (e.target.classList.contains('option-input') && e.key === 'Enter') {
                e.preventDefault();
                const groupIndex = e.target.closest('.variant-options-container').dataset.group;
                addOptionToGroup(parseInt(groupIndex));
            }
        });

        // Thêm group mặc định khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            // Có thể bỏ comment dòng này nếu muốn có sẵn 1 group
            // addVariantGroup();
            
            // Thêm validation vào form submit
            const productForm = document.getElementById('productForm');
            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>
