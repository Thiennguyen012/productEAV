<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->product_name ?? 'Chi tiết sản phẩm' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-image-main {
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .variant-option {
            cursor: pointer;
            transition: all 0.2s;
        }
        .variant-option:hover {
            transform: scale(1.05);
        }
        .variant-option.selected {
            border: 2px solid #0d6efd !important;
            background-color: #0d6efd;
            color: white;
        }
        .price-display {
            font-size: 1.5rem;
            font-weight: bold;
            color: #e74c3c;
        }
        .compare-price {
            text-decoration: line-through;
            color: #95a5a6;
            font-size: 1rem;
        }
        .stock-status {
            font-size: 0.9rem;
        }
        .variant-selection {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .variant-option.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }
        .variant-option.disabled:hover {
            transform: none;
        }
    </style>
</head>
<body>
    @if($product)
    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.showAll') }}">Sản phẩm</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('products.showAll') }}?category={{ $product->category->id }}">{{ $product->category->category_name }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->product_name }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="position-relative">
                    <img src="{{ asset('images/' . $product->image) }}" 
                         class="img-fluid product-image-main w-100" 
                         alt="{{ $product->product_name }}"
                         id="mainProductImage">
                    
                    @if($product->is_active === 'false')
                        <span class="badge bg-danger position-absolute top-0 end-0 m-3 fs-6">
                            Ngừng bán
                        </span>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="product-info">
                    <!-- Category Badge -->
                    @if($product->category)
                        <span class="badge bg-info mb-2">{{ $product->category->category_name }}</span>
                    @endif
                    
                    <!-- Product Name -->
                    <h1 class="h2 mb-3">{{ $product->product_name }}</h1>
                    
                    <!-- Description -->
                    <p class="text-muted mb-4">{{ $product->description }}</p>

                    <!-- Price Display -->
                    <div class="price-section mb-4">
                        <div class="price-display" id="priceDisplay">
                            @if($product->variants && $product->variants->count() > 0)
                                @php
                                    $minPrice = $product->variants->min('price');
                                    $maxPrice = $product->variants->max('price');
                                @endphp
                                @if($minPrice == $maxPrice)
                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                @else
                                    {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                @endif
                            @else
                                <span class="text-muted">Chưa có giá</span>
                            @endif
                        </div>
                        <div class="compare-price" id="comparePriceDisplay" style="display: none;"></div>
                    </div>

                    <!-- Variant Selection -->
                    @if($product->variantGroups && $product->variantGroups->count() > 0)
                        <div class="variant-selection-section mb-4">
                            <h5 class="mb-3">Chọn tùy chọn sản phẩm:</h5>
                            
                            @foreach($product->variantGroups as $group)
                                <div class="variant-selection">
                                    <h6 class="mb-3">{{ $group->name }}:</h6>
                                    <div class="row g-2">
                                        @foreach($group->options as $option)
                                            <div class="col-auto">
                                                <div class="variant-option badge bg-light text-dark border p-2" 
                                                     data-group="{{ $group->id }}" 
                                                     data-option="{{ $option->id }}"
                                                     data-value="{{ $option->value }}">
                                                    {{ $option->value }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Selected Variant Info -->
                    <div class="selected-variant-info mb-4" id="selectedVariantInfo" style="display: none;">
                        <div class="alert alert-success">
                            <h6 class="mb-2">Biến thể đã chọn:</h6>
                            <div id="selectedVariantDetails"></div>
                        </div>
                    </div>

                    <!-- Stock Status -->
                    <div class="stock-status mb-4" id="stockStatus">
                        @if($product->variants && $product->variants->count() > 0)
                            @php
                                $totalStock = $product->variants->sum('quantity');
                            @endphp
                            @if($totalStock > 0)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Còn hàng ({{ $totalStock }} sản phẩm)
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle me-1"></i>
                                    Hết hàng
                                </span>
                            @endif
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Chưa có biến thể
                            </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="action-buttons">
                        <div class="row g-2">
                            <div class="col-12">
                                <button class="btn btn-primary btn-lg w-100" id="addToCartBtn" disabled>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Thêm vào giỏ hàng
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-danger w-100">
                                    <i class="fas fa-heart me-1"></i>
                                    Yêu thích
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-outline-info w-100">
                                    <i class="fas fa-share me-1"></i>
                                    Chia sẻ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Variants Table -->
        @if($product->variants && $product->variants->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Tất cả biến thể có sẵn ({{ $product->variants->count() }})
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SKU</th>
                                            <th>Tùy chọn</th>
                                            <th>Giá bán</th>
                                            <th>Giá so sánh</th>
                                            <th>Kho</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($product->variants as $variant)
                                            <tr data-variant-id="{{ $variant->id }}">
                                                <td><code>{{ $variant->sku }}</code></td>
                                                <td>
                                                    @if($variant->options && $variant->options->count() > 0)
                                                        @foreach($variant->options as $option)
                                                            <span class="badge bg-secondary me-1">{{ $option->value }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong class="text-success">{{ number_format($variant->price, 0, ',', '.') }}₫</strong>
                                                </td>
                                                <td>
                                                    @if($variant->compare_at_price && $variant->compare_at_price > $variant->price)
                                                        <span class="text-muted">{{ number_format($variant->compare_at_price, 0, ',', '.') }}₫</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $variant->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $variant->quantity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($variant->is_active === 'true' && $variant->quantity > 0)
                                                        <span class="badge bg-success">Có sẵn</span>
                                                    @else
                                                        <span class="badge bg-danger">Hết hàng</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($variant->is_active === 'true' && $variant->quantity > 0)
                                                        <button class="btn btn-sm btn-outline-primary select-variant-btn" 
                                                                data-variant-data="{{ json_encode($variant) }}">
                                                            Chọn
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            Không có sẵn
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('products.showAll') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Quay lại danh sách sản phẩm
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Product Not Found -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-12 text-center">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Không tìm thấy sản phẩm</h4>
                    <p>Sản phẩm bạn đang tìm không tồn tại hoặc đã bị xóa.</p>
                    <a href="{{ route('products.showAll') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Quay lại danh sách sản phẩm
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedOptions = {};
        const variants = @json($product->variants ?? []);
        const variantGroups = @json($product->variantGroups ?? []);

        // Handle variant option selection
        document.querySelectorAll('.variant-option').forEach(option => {
            option.addEventListener('click', function() {
                // Skip if option is disabled
                if (this.classList.contains('disabled')) {
                    return;
                }

                const groupId = this.dataset.group;
                const optionId = this.dataset.option;
                const optionValue = this.dataset.value;

                // Check if this option is already selected
                const isAlreadySelected = this.classList.contains('selected');

                if (isAlreadySelected) {
                    // Unselect this option
                    this.classList.remove('selected');
                    delete selectedOptions[groupId];
                } else {
                    // Remove selected class from other options in same group
                    document.querySelectorAll(`[data-group="${groupId}"]`).forEach(el => {
                        el.classList.remove('selected');
                    });

                    // Add selected class to clicked option
                    this.classList.add('selected');

                    // Store selection
                    selectedOptions[groupId] = {
                        id: optionId,
                        value: optionValue
                    };
                }

                // Update available options based on current selection
                updateAvailableOptions();

                // Find matching variant
                updateSelectedVariant();
            });
        });

        // Handle direct variant selection from table
        document.querySelectorAll('.select-variant-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const variantData = JSON.parse(this.dataset.variantData);
                selectVariantFromTable(variantData);
            });
        });

        function updateSelectedVariant() {
            // Check if we have selections for all groups
            const totalGroups = variantGroups.length;
            const selectedGroups = Object.keys(selectedOptions).length;

            if (selectedGroups === totalGroups) {
                // Find matching variant
                const matchingVariant = findMatchingVariant();
                if (matchingVariant) {
                    displaySelectedVariant(matchingVariant);
                    enableAddToCart();
                }
            } else {
                hideSelectedVariant();
                disableAddToCart();
            }
        }

        function findMatchingVariant() {
            return variants.find(variant => {
                if (!variant.options || variant.options.length === 0) return false;
                
                const selectedOptionIds = Object.values(selectedOptions).map(opt => parseInt(opt.id));
                const variantOptionIds = variant.options.map(opt => opt.id);
                
                return selectedOptionIds.every(id => variantOptionIds.includes(id)) &&
                       variantOptionIds.every(id => selectedOptionIds.includes(id));
            });
        }

        function selectVariantFromTable(variant) {
            // Clear previous selections
            document.querySelectorAll('.variant-option').forEach(el => {
                el.classList.remove('selected');
            });
            selectedOptions = {};

            // Set selections based on variant options
            if (variant.options && variant.options.length > 0) {
                variant.options.forEach(option => {
                    // Find the corresponding option element and select it
                    const optionElement = document.querySelector(`[data-option="${option.id}"]`);
                    if (optionElement) {
                        optionElement.classList.add('selected');
                        const groupId = optionElement.dataset.group;
                        selectedOptions[groupId] = {
                            id: option.id,
                            value: option.value
                        };
                    }
                });
            }

            // Update available options based on selection
            updateAvailableOptions();

            displaySelectedVariant(variant);
            enableAddToCart();
        }

        function displaySelectedVariant(variant) {
            const infoDiv = document.getElementById('selectedVariantInfo');
            const detailsDiv = document.getElementById('selectedVariantDetails');
            const priceDisplay = document.getElementById('priceDisplay');
            const comparePriceDisplay = document.getElementById('comparePriceDisplay');
            const stockStatus = document.getElementById('stockStatus');

            // Show selected variant info
            detailsDiv.innerHTML = `
                <strong>SKU:</strong> ${variant.sku}<br>
                <strong>Tùy chọn:</strong> ${Object.values(selectedOptions).map(opt => opt.value).join(', ')}<br>
                <strong>Giá:</strong> <span class="text-success">${new Intl.NumberFormat('vi-VN').format(variant.price)}₫</span><br>
                <strong>Kho:</strong> ${variant.quantity} sản phẩm
            `;
            infoDiv.style.display = 'block';

            // Update price display
            priceDisplay.textContent = `${new Intl.NumberFormat('vi-VN').format(variant.price)}₫`;
            
            // Show compare price if exists
            if (variant.compare_at_price && variant.compare_at_price > variant.price) {
                comparePriceDisplay.textContent = `${new Intl.NumberFormat('vi-VN').format(variant.compare_at_price)}₫`;
                comparePriceDisplay.style.display = 'block';
            } else {
                comparePriceDisplay.style.display = 'none';
            }

            // Update stock status
            if (variant.quantity > 0) {
                stockStatus.innerHTML = `
                    <span class="badge bg-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Còn hàng (${variant.quantity} sản phẩm)
                    </span>
                `;
            } else {
                stockStatus.innerHTML = `
                    <span class="badge bg-danger">
                        <i class="fas fa-times-circle me-1"></i>
                        Hết hàng
                    </span>
                `;
            }
        }

        function hideSelectedVariant() {
            document.getElementById('selectedVariantInfo').style.display = 'none';
        }

        function enableAddToCart() {
            document.getElementById('addToCartBtn').disabled = false;
        }

        function disableAddToCart() {
            document.getElementById('addToCartBtn').disabled = true;
        }

        // New function to update available options based on current selection
        function updateAvailableOptions() {
            // Get all possible option combinations based on current selections
            const availableOptionIds = getAvailableOptionIds();

            // Update each option's availability
            document.querySelectorAll('.variant-option').forEach(option => {
                const optionId = parseInt(option.dataset.option);
                const groupId = option.dataset.group;
                
                // Don't disable options in already selected groups (allow unselecting)
                if (selectedOptions[groupId] && selectedOptions[groupId].id == optionId) {
                    option.classList.remove('disabled');
                    return;
                }

                // Check if this option can be combined with current selections
                if (availableOptionIds.includes(optionId)) {
                    option.classList.remove('disabled');
                } else {
                    option.classList.add('disabled');
                }
            });
        }

        function getAvailableOptionIds() {
            const availableOptionIds = new Set();

            // If no selections, only show options that have stock
            if (Object.keys(selectedOptions).length === 0) {
                variants.forEach(variant => {
                    // Only consider variants that are active and have stock
                    if (variant.options && variant.options.length > 0 && 
                        variant.is_active === 'true' && variant.quantity > 0) {
                        variant.options.forEach(opt => availableOptionIds.add(opt.id));
                    }
                });
                return Array.from(availableOptionIds);
            }

            // For each variant group, find compatible options
            variantGroups.forEach(group => {
                const groupId = group.id.toString();
                
                // Skip if this group is already selected (allow changing selection)
                if (selectedOptions[groupId]) {
                    availableOptionIds.add(parseInt(selectedOptions[groupId].id));
                }
                
                // Find options in this group that are compatible with current selections
                group.options.forEach(option => {
                    // Check if this option can be combined with current selections
                    const isCompatible = variants.some(variant => {
                        // Only consider variants that are active and have stock
                        if (!variant.options || variant.options.length === 0 || 
                            variant.is_active !== 'true' || variant.quantity <= 0) return false;
                        
                        const variantOptionIds = variant.options.map(opt => opt.id);
                        
                        // Must contain this option
                        if (!variantOptionIds.includes(option.id)) return false;
                        
                        // Must be compatible with all current selections from other groups
                        for (const [selectedGroupId, selectedOption] of Object.entries(selectedOptions)) {
                            // Skip if this is the same group (we're checking if we can select this option)
                            if (selectedGroupId === groupId) continue;
                            
                            // This variant must also contain the selected option from other groups
                            if (!variantOptionIds.includes(parseInt(selectedOption.id))) {
                                return false;
                            }
                        }
                        
                        return true;
                    });
                    
                    if (isCompatible) {
                        availableOptionIds.add(option.id);
                    }
                });
            });

            return Array.from(availableOptionIds);
        }

        function clearSelections() {
            // Clear all selections
            selectedOptions = {};
            
            // Remove selected class from all options
            document.querySelectorAll('.variant-option').forEach(el => {
                el.classList.remove('selected');
                el.classList.remove('disabled');
            });
            
            // Hide selected variant info
            hideSelectedVariant();
            disableAddToCart();
            
            // Reset price display
            const priceDisplay = document.getElementById('priceDisplay');
            const comparePriceDisplay = document.getElementById('comparePriceDisplay');
            
            @if($product->variants && $product->variants->count() > 0)
                @php
                    $minPrice = $product->variants->min('price');
                    $maxPrice = $product->variants->max('price');
                @endphp
                @if($minPrice == $maxPrice)
                    priceDisplay.textContent = '{{ number_format($minPrice, 0, ',', '.') }}₫';
                @else
                    priceDisplay.textContent = '{{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫';
                @endif
            @else
                priceDisplay.innerHTML = '<span class="text-muted">Chưa có giá</span>';
            @endif
            
            comparePriceDisplay.style.display = 'none';
        }

        // Add clear selection button functionality if needed
        function addClearButton() {
            const selectionSection = document.querySelector('.variant-selection-section');
            if (selectionSection && !document.getElementById('clearSelectionsBtn')) {
                const clearBtn = document.createElement('button');
                clearBtn.id = 'clearSelectionsBtn';
                clearBtn.className = 'btn btn-outline-secondary btn-sm mb-3';
                clearBtn.innerHTML = '<i class="fas fa-times me-1"></i>Xóa lựa chọn';
                clearBtn.addEventListener('click', clearSelections);
                
                selectionSection.insertBefore(clearBtn, selectionSection.firstChild.nextSibling);
            }
        }

        // Initialize clear button
        if (variantGroups.length > 0) {
            addClearButton();
        }

        // Initialize available options on page load (hide options without stock)
        document.addEventListener('DOMContentLoaded', function() {
            updateAvailableOptions();
        });
    </script>
</body>
</html>
