<?php

namespace App\Repositories;

use App\Repositories\Interfaces\IProductRepo;
use App\Models\Product\Product;

class ProductRepo extends BaseRepo implements IProductRepo
{
    public function __construct(Product $product)
    {
        parent::__construct($product);
    }

    public function listProduct()
    {
        return $this->model->with(['category', 'variants.options', 'variantGroups.options'])->paginate(12);
    }

    public function getProductWithVariants($slug)
    {
        return $this->model->with([
            'category',
            'variants' => function ($query) {
                $query->where('is_active', 'true');
            },
            'variants.options',
            'variantGroups.options'
        ])->where('slug', $slug)->first();
    }

    public function getProductsByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId)
            ->where('is_active', 'true')
            ->with(['category', 'variants'])
            ->get();
    }
}
