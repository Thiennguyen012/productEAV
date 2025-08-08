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

    // Ví dụ hàm sinh SKU tự động
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
}
