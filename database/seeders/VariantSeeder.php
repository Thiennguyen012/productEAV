<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product\Product;
use App\Models\Product\VariantGroup;
use App\Models\Product\VariantOption;
use App\Models\Product\ProductVariant;
use App\Models\Product\ProductVariantValue;

class VariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        // Định nghĩa variant groups cho từng loại sản phẩm
        $variantGroupsData = [
            'common' => [
                [
                    'name' => 'Màu sắc',
                    'options' => ['Đen', 'Trắng', 'Xám', 'Xanh Navy', 'Đỏ']
                ],
                [
                    'name' => 'Kích thước',
                    'options' => ['S', 'M', 'L', 'XL', 'XXL']
                ]
            ],
            'shoes' => [
                [
                    'name' => 'Màu sắc',
                    'options' => ['Đen', 'Nâu', 'Trắng']
                ],
                [
                    'name' => 'Size',
                    'options' => ['38', '39', '40', '41', '42']
                ]
            ],
            'accessories' => [
                [
                    'name' => 'Màu sắc',
                    'options' => ['Đen', 'Nâu', 'Đỏ', 'Xanh']
                ],
                [
                    'name' => 'Chất liệu',
                    'options' => ['Da thật', 'Da PU', 'Vải Canvas']
                ]
            ]
        ];

        foreach ($products as $product) {
            // Xác định loại variant groups dựa trên tên sản phẩm
            $groupType = 'common';
            if (
                strpos($product->product_name, 'Túi') !== false ||
                strpos($product->product_name, 'Đồng Hồ') !== false ||
                strpos($product->product_name, 'Kính') !== false
            ) {
                $groupType = 'accessories';
            }

            $groups = $variantGroupsData[$groupType];
            $createdGroups = [];

            // Tạo variant groups cho sản phẩm
            foreach ($groups as $groupData) {
                $variantGroup = VariantGroup::create([
                    'name' => $groupData['name'],
                    'product_id' => $product->id
                ]);

                $createdOptions = [];
                // Tạo variant options cho group
                foreach ($groupData['options'] as $optionValue) {
                    $option = VariantOption::create([
                        'variant_group_id' => $variantGroup->id,
                        'value' => $optionValue
                    ]);
                    $createdOptions[] = $option;
                }

                $createdGroups[] = [
                    'group' => $variantGroup,
                    'options' => $createdOptions
                ];
            }

            // Tạo product variants (kết hợp các options)
            if (count($createdGroups) >= 2) {
                $colorOptions = $createdGroups[0]['options'];
                $sizeOptions = $createdGroups[1]['options'];

                // Tạo một số variant combinations (không tạo tất cả để tránh quá nhiều)
                $combinations = [
                    [$colorOptions[0], $sizeOptions[0]], // Màu đầu tiên + Size đầu tiên
                    [$colorOptions[0], $sizeOptions[1]], // Màu đầu tiên + Size thứ hai
                    [$colorOptions[1], $sizeOptions[0]], // Màu thứ hai + Size đầu tiên
                    [$colorOptions[1], $sizeOptions[1]], // Màu thứ hai + Size thứ hai
                ];

                foreach ($combinations as $index => $combination) {
                    $productVariant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $product->slug . '-' . ($index + 1),
                        'price' => rand(100000, 500000), // Giá ngẫu nhiên từ 100k-500k
                        'compare_at_price' => rand(600000, 800000), // Giá so sánh
                        'quantity' => rand(10, 100), // Số lượng ngẫu nhiên
                        'is_active' => 'true',
                        'image' => $product->image
                    ]);

                    // Tạo product variant values (liên kết với options)
                    foreach ($combination as $option) {
                        ProductVariantValue::create([
                            'product_variant_id' => $productVariant->id,
                            'variant_option_id' => $option->id
                        ]);
                    }
                }
            }
        }
    }
}
