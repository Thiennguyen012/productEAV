<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Repositories\Interfaces\IProductRepo;
use App\Repositories\Interfaces\IVariantGroupRepo;
use App\Repositories\Interfaces\IVariantOptionRepo;
use App\Services\Interfaces\IProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductService implements IProductService
{
    protected $productRepo;
    protected $variantGroupRepo;
    protected $variantOptionRepo;
    public function __construct(IProductRepo $productRepo, IVariantGroupRepo $variantGroupRepo, IVariantOptionRepo $variantOptionRepo)
    {
        $this->productRepo = $productRepo;
        $this->variantGroupRepo = $variantGroupRepo;
        $this->variantOptionRepo = $variantOptionRepo;
    }
    public function listProduct()
    {
        $result = $this->productRepo->listProduct();
        return $result;
    }
    public function getProductWithVariants(Request $request)
    {
        $slug = $request->input('slug');
        $result = $this->productRepo->getProductWithVariants($slug);
        return $result;
    }

    // Hàm sinh SKU tự động
    protected function generateSku($product, $optionIds)
    {
        // Lấy tên option, nối lại thành SKU theo format: product_id-option1-option2-...
        $optionNames = \App\Models\Product\VariantOption::whereIn('id', $optionIds)->pluck('value')->toArray();

        // Bắt đầu SKU bằng ID sản phẩm
        $sku = $product->id;

        if (!empty($optionNames)) {
            // Thêm các option vào SKU: product_id-option1-option2-...
            $sku .= '-' . implode('-', $optionNames);
        } else {
            // Nếu không có option, thêm unique ID để tránh trùng lặp
            $sku .= '-' . uniqid();
        }

        return $sku;
    }
    //crud
    public function newProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            // 1. Tạo sản phẩm
            $newProductArr = [
                'product_name' => $request->input('product_name'),
                'slug' => Str::slug($request->input('product_name')),
                'description' => $request->input('description'),
                'image' => $request->input('image', 'default-product.jpg'), // Provide default image if none provided
                'is_active' => $request->input('is_active'),
                'category_id' => $request->input('category_id'),
            ];
            $product = $this->productRepo->create($newProductArr);

            // 2. Tạo variant group và option, lưu lại id các option theo group
            $variantGroups = $request->input('variant_groups', []);
            $optionIdsByGroup = [];
            foreach ($variantGroups as $groupData) {
                $group = $this->variantGroupRepo->create([
                    'name' => $groupData['name'],
                    'product_id' => $product->id,
                ]);
                $optionIdsByGroup[$group->id] = [];
                if (!empty($groupData['options']) && is_array($groupData['options'])) {
                    foreach ($groupData['options'] as $optionName) {
                        $option = $this->variantOptionRepo->create([
                            'value' => $optionName,
                            'variant_group_id' => $group->id,
                        ]);
                        $optionIdsByGroup[$group->id][] = $option->id;
                    }
                }
            }

            // 3. Sinh tất cả tổ hợp các option (cartesian product)
            $optionGroups = array_values($optionIdsByGroup);
            $combinations = [[]];
            foreach ($optionGroups as $groupOptions) {
                $tmp = [];
                foreach ($combinations as $combination) {
                    foreach ($groupOptions as $optionId) {
                        $tmp[] = array_merge($combination, [$optionId]);
                    }
                }
                $combinations = $tmp;
            }

            // 4. Tạo product_variant và product_variant_value cho từng tổ hợp
            $variantData = $request->input('variants', []);
            foreach ($combinations as $index => $optionIds) {
                // Lấy thông tin variant từ form (nếu có)
                $currentVariantData = $variantData[$index] ?? [];

                // Xử lý upload hình ảnh variant
                $variantImagePath = 'default-variant.jpg';
                if ($request->hasFile("variants.{$index}.image")) {
                    $image = $request->file("variants.{$index}.image");
                    $variantImagePath = $image->store('variants', 'public');
                }

                // Tạo variant với dữ liệu từ form
                $variant = $product->variants()->create([
                    'sku' => $this->generateSku($product, $optionIds),
                    'price' => $currentVariantData['price'] ?? 0,
                    'compare_at_price' => $currentVariantData['compare_at_price'] ?? 0,
                    'quantity' => $currentVariantData['quantity'] ?? 0,
                    'is_active' => $currentVariantData['is_active'] ?? 'true',
                    'image' => $variantImagePath,
                ]);

                // Gán các option cho variant
                foreach ($optionIds as $optionId) {
                    $variant->values()->create([
                        'variant_option_id' => $optionId,
                    ]);
                }
            }

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getProductForEdit($id)
    {
        return $this->productRepo->getProductWithVariantsAndGroupsById($id);
    }

    public function updateProduct(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepo->find($id);

            // 1. Cập nhật thông tin sản phẩm
            $productData = [
                'product_name' => $request->input('product_name'),
                'slug' => Str::slug($request->input('product_name')),
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'is_active' => $request->input('is_active'),
            ];

            // Xử lý ảnh sản phẩm
            if ($request->hasFile('image')) {
                $productData['image'] = $request->file('image')->store('products', 'public');
            }

            $this->productRepo->update($id, $productData);

            // 2. Xử lý variant groups (thêm/sửa/xóa)
            $this->handleVariantGroups($request, $product);

            // 3. Xử lý variants hiện có
            $this->handleProductVariants($request, $product);

            // 4. Tự động tạo variants mới từ combinations
            $this->regenerateProductVariants($product);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function handleVariantGroups(Request $request, $product)
    {
        $variantGroups = $request->input('variant_groups', []);
        $existingGroupIds = [];

        foreach ($variantGroups as $groupData) {
            if (isset($groupData['id']) && $groupData['id']) {
                // Cập nhật group existing
                $group = $this->variantGroupRepo->update($groupData['id'], [
                    'name' => $groupData['name']
                ]);
                $existingGroupIds[] = $groupData['id'];
            } else {
                // Tạo group mới
                $group = $this->variantGroupRepo->create([
                    'name' => $groupData['name'],
                    'product_id' => $product->id,
                ]);
                $existingGroupIds[] = $group->id;
            }

            // Xử lý options cho group này
            $this->handleVariantOptions($groupData, $group);
        }

        // Xóa các groups không còn tồn tại
        $this->deleteRemovedGroups($product->id, $existingGroupIds);
    }

    private function handleVariantOptions($groupData, $group)
    {
        $options = $groupData['options'] ?? [];
        $existingOptionIds = [];

        foreach ($options as $optionData) {
            if (is_array($optionData) && isset($optionData['id']) && $optionData['id']) {
                // Cập nhật option existing
                $this->variantOptionRepo->update($optionData['id'], [
                    'value' => $optionData['value']
                ]);
                $existingOptionIds[] = $optionData['id'];
            } else {
                // Tạo option mới (string value)
                $option = $this->variantOptionRepo->create([
                    'value' => is_string($optionData) ? $optionData : $optionData['value'],
                    'variant_group_id' => $group->id,
                ]);
                $existingOptionIds[] = $option->id;
            }
        }

        // Xóa các options không còn tồn tại
        $this->deleteRemovedOptions($group->id, $existingOptionIds);
    }

    private function handleProductVariants(Request $request, $product)
    {
        $variantData = $request->input('variants', []);

        foreach ($variantData as $index => $variant) {
            if (isset($variant['id']) && $variant['id']) {
                // Cập nhật variant existing
                $updateData = [
                    'price' => $variant['price'] ?? 0,
                    'compare_at_price' => $variant['compare_at_price'] ?? 0,
                    'quantity' => $variant['quantity'] ?? 0,
                    'is_active' => $variant['is_active'] ?? 'true',
                ];

                // Xử lý ảnh variant
                if ($request->hasFile("variants.{$index}.image")) {
                    $updateData['image'] = $request->file("variants.{$index}.image")->store('variants', 'public');
                }

                $product->variants()->where('id', $variant['id'])->update($updateData);
            }
            // Note: Tạo variant mới sẽ được xử lý khi thay đổi combinations
        }
    }

    private function deleteRemovedOptions($groupId, $existingOptionIds)
    {
        // Lấy tất cả option IDs hiện tại của group
        $currentOptionIds = $this->variantOptionRepo->getOptionIdsByGroupId($groupId);

        // Tìm các option cần xóa (có trong DB nhưng không có trong $existingOptionIds)
        $optionIdsToDelete = array_diff($currentOptionIds, $existingOptionIds);

        if (!empty($optionIdsToDelete)) {
            // Xóa các variant values liên quan trước
            $this->productRepo->deleteVariantValuesByOptionIds($optionIdsToDelete);

            // Sau đó xóa các options
            $this->variantOptionRepo->deleteByIds($optionIdsToDelete);
        }
    }
    private function deleteRemovedGroups($productId, $existingGroupIds)
    {
        // Lấy tất cả group IDs hiện tại của product
        $currentGroupIds = $this->variantGroupRepo->getGroupIdsByProductId($productId);

        // Tìm các group cần xóa
        $groupIdsToDelete = array_diff($currentGroupIds, $existingGroupIds);

        if (!empty($groupIdsToDelete)) {
            // Xóa các variant values liên quan
            $this->productRepo->deleteVariantValuesByGroupIds($groupIdsToDelete);

            // Xóa các options của groups
            $this->variantOptionRepo->deleteByGroupIds($groupIdsToDelete);

            // Xóa các groups
            $this->variantGroupRepo->deleteByIds($groupIdsToDelete);
        }
    }

    /**
     * Tự động tạo lại các product variants dựa trên combinations của variant groups
     */
    private function regenerateProductVariants($product)
    {
        // Lấy tất cả variant groups và options của product
        $variantGroups = $this->variantGroupRepo->getGroupsWithOptionsByProductId($product->id);

        if ($variantGroups->isEmpty()) {
            return;
        }

        // Tạo tất cả combinations có thể
        $allOptions = [];
        foreach ($variantGroups as $group) {
            $groupOptions = [];
            foreach ($group->options as $option) {
                $groupOptions[] = $option->id;
            }
            if (!empty($groupOptions)) {
                $allOptions[] = $groupOptions;
            }
        }

        if (empty($allOptions)) {
            return;
        }

        // Generate cartesian product
        $combinations = $this->cartesianProduct($allOptions);

        // Tạo variants cho mỗi combination
        foreach ($combinations as $combination) {
            // Kiểm tra variant đã tồn tại chưa
            if (!$this->productRepo->variantExistsWithOptions($product->id, $combination)) {
                // Tạo variant mới
                $sku = $this->generateSku($product, $combination);

                $variant = $this->productRepo->createVariant([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'price' => 0,
                    'compare_at_price' => 0,
                    'quantity' => 0,
                    'is_active' => 'true',
                    'image' => 'default-variant.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Tạo variant values
                foreach ($combination as $optionId) {
                    $this->productRepo->createVariantValue([
                        'product_variant_id' => $variant,
                        'variant_option_id' => $optionId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Tạo cartesian product của các arrays
     */
    private function cartesianProduct($arrays)
    {
        $result = [[]];

        foreach ($arrays as $property => $values) {
            $append = [];
            foreach ($result as $product) {
                foreach ($values as $item) {
                    $product[$property] = $item;
                    $append[] = $product;
                }
            }
            $result = $append;
        }

        return $result;
    }
}
