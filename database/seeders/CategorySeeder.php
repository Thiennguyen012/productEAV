<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Thời trang Nam',
                'slug' => 'thoi-trang-nam',
                'description' => 'Bộ sưu tập thời trang dành cho nam giới với những thiết kế hiện đại và chất lượng cao'
            ],
            [
                'category_name' => 'Thời trang Nữ',
                'slug' => 'thoi-trang-nu',
                'description' => 'Bộ sưu tập thời trang dành cho nữ giới với phong cách đa dạng và thời thượng'
            ],
            [
                'category_name' => 'Phụ kiện',
                'slug' => 'phu-kien',
                'description' => 'Các món phụ kiện thời trang để hoàn thiện phong cách của bạn'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
