<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        // Dữ liệu sản phẩm cho từng category
        $productsData = [
            1 => [ // Thời trang Nam
                [
                    'product_name' => 'Áo Sơ Mi Nam Công Sở',
                    'slug' => 'ao-so-mi-nam-cong-so',
                    'description' => 'Áo sơ mi nam công sở chất liệu cotton cao cấp, phù hợp cho môi trường làm việc chuyên nghiệp',
                    'image' => 'ao-so-mi-nam.jpg'
                ],
                [
                    'product_name' => 'Quần Jean Nam Slim Fit',
                    'slug' => 'quan-jean-nam-slim-fit',
                    'description' => 'Quần jean nam form slim fit, chất liệu denim co giãn thoải mái',
                    'image' => 'quan-jean-nam.jpg'
                ],
                [
                    'product_name' => 'Áo Thun Nam Basic',
                    'slug' => 'ao-thun-nam-basic',
                    'description' => 'Áo thun nam basic chất cotton 100%, form regular thoải mái',
                    'image' => 'ao-thun-nam.jpg'
                ]
            ],
            2 => [ // Thời trang Nữ
                [
                    'product_name' => 'Đầm Nữ Công Sở',
                    'slug' => 'dam-nu-cong-so',
                    'description' => 'Đầm nữ công sở thanh lịch, phù hợp cho môi trường làm việc',
                    'image' => 'dam-nu-cong-so.jpg'
                ],
                [
                    'product_name' => 'Chân Váy Nữ A-Line',
                    'slug' => 'chan-vay-nu-a-line',
                    'description' => 'Chân váy nữ dáng A-line cổ điển, dễ phối đồ',
                    'image' => 'chan-vay-nu.jpg'
                ],
                [
                    'product_name' => 'Áo Blouse Nữ',
                    'slug' => 'ao-blouse-nu',
                    'description' => 'Áo blouse nữ chất liệu voan mềm mại, thiết kế nữ tính',
                    'image' => 'ao-blouse-nu.jpg'
                ]
            ],
            3 => [ // Phụ kiện
                [
                    'product_name' => 'Túi Xách Da Cao Cấp',
                    'slug' => 'tui-xach-da-cao-cap',
                    'description' => 'Túi xách da thật cao cấp, thiết kế sang trọng và bền bỉ',
                    'image' => 'tui-xach-da.jpg'
                ],
                [
                    'product_name' => 'Đồng Hồ Thời Trang',
                    'slug' => 'dong-ho-thoi-trang',
                    'description' => 'Đồng hồ thời trang với nhiều màu sắc và phong cách',
                    'image' => 'dong-ho.jpg'
                ],
                [
                    'product_name' => 'Kính Mắt UV Protection',
                    'slug' => 'kinh-mat-uv-protection',
                    'description' => 'Kính mắt chống tia UV, bảo vệ mắt và tăng phong cách',
                    'image' => 'kinh-mat.jpg'
                ]
            ]
        ];

        foreach ($categories as $category) {
            if (isset($productsData[$category->id])) {
                foreach ($productsData[$category->id] as $productData) {
                    Product::create(array_merge($productData, [
                        'category_id' => $category->id,
                        'is_active' => 'true'
                    ]));
                }
            }
        }
    }
}
